<?php
$pageTitle = 'Gestion des devises';
$moreJs = array('formManager');

if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    //Si on demande la création d'une devise
    if(!empty($_POST['createCurrency'])){
        //Si on a les droits pour créer une devise
        if($user->getType() === 1){
            $data = $_POST['createCurrency'];

            createCurrency($data);
        }
        else{
            $_SESSION['returnAction'] = array(0, 'Erreur : Vous n\'avez pas les droits nécessaires pour créer une devise.');
        }
    }

    //Si on demande la modification d'une devise
    else if(!empty($_POST['modifyCurrency'])){
        if($user->getType() === 1){
            $data = $_POST['modifyCurrency'];

            modifyCurrency($data);
        }
        else{
            $_SESSION['returnAction'] = array(0, 'Erreur : Vous n\'avez pas les droits nécessaires pour modifier cette devise.');
        }
    }
}


/**
 * @param array $data
 *
 * @return bool
 */
function createCurrency(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $name = (string)$data['name'];
        $abbreviation = (string)$data['abbreviation'];
        $symbol = (string)$data['symbol'];
        $conversionFromEuro = (float)$data['conversionFromEuro'];
        $conversionToEuro = (float)$data['conversionToEuro'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($name) || empty($abbreviation) || empty($symbol) || empty($conversionFromEuro) || empty($conversionToEuro)){
            throw new Exception('Un ou plusieurs champs requis est(sont) vide(s)');
        }
        if(!verifyPureString($name)){
            throw new Exception('Le nom de la devise est incorrect');
        }
        if(!verifyPureString($abbreviation)){
            throw new Exception('L\'abbreviation de la devise est incorrecte');
        }
        if($conversionFromEuro != (1/$conversionToEuro)){
            throw new Exception('Les taux de change entrés sont incorrects');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée jusqu'ici, cohérence des données
        //On créé en local la monnaie
        $currency = new \Prices\Currency(0, $name, $abbreviation, $symbol, null);

        //Puis on l'insère en base
        $insertCurrency = \Prices\CurrencyManager::insertCurrency($db, $currency);

        //Si insertion mal passée
        if(is_a($insertCurrency, '\Exception')){
            throw new Exception($insertCurrency->getMessage(), $insertCurrency->getCode());
        }
        //On récupère l'ID
        $currency->setId($insertCurrency->getId());

        //Puis on créé le taux de change
        $exchangeRate = new \Prices\ExchangeRate(0, $conversionToEuro, new DateTime());

        //Et on l'insère en base
        $insertExchangeRate = \Prices\ExchangeRateManager::insertExchangeRate($db, $exchangeRate, $currency->getId());

        //Si erreur lors de l'insertion
        if(is_a($insertExchangeRate, '\Exception')){
            throw new Exception($insertExchangeRate->getMessage(), $insertExchangeRate->getCode());
        }

        //On commit les changements
        $db->commit();
        $db = null;

        //Et message de succès
        $_SESSION['returnAction'] = array(1, 'Création de la nouvelle devise effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage());
        $db = null;

        return false;
    }
}

/**
 * @param array $data
 *
 * @return bool
 */
function modifyCurrency(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $currencyId = (int)$data['id'];
        $name = (string)$data['name'];
        $abbreviation = (string)$data['abbreviation'];
        $symbol = (string)$data['symbol'];
        $conversionFromEuro = (float)$data['conversionFromEuro'];
        $conversionToEuro = (float)$data['conversionToEuro'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($currencyId) || empty($name) || empty($abbreviation) || empty($symbol) || empty($conversionFromEuro) || empty($conversionToEuro)){
            throw new Exception('Un ou plusieurs champs requis est(sont) vide(s)');
        }
        if(!verifyPureString($name)){
            throw new Exception('Le nom de la devise est incorrect');
        }
        if(!verifyPureString($abbreviation)){
            throw new Exception('L\'abbreviation de la devise est incorrecte');
        }
        if($conversionFromEuro != (1/$conversionToEuro)){
            throw new Exception('Les taux de change entrés sont incorrects');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée jusqu'ici, cohérence des données
        //On récupère la devise en base
        $currency = \Prices\CurrencyManager::fetchCurrency($db, $currencyId);
        //Si erreur lors de la recherche
        if(is_a($currency, '\Exception')){
            throw new Exception($currency->getMessage(), $currency->getCode());
        }

        //Si une donnée de la devise a été modifiée
        if( $currency->getCurrency() != $name ||
            $currency->getAbbreviation() != $abbreviation ||
            $currency->getSymbol() != $symbol){
            //On la modifie en local
            $currency->setCurrency($name);
            $currency->setAbbreviation($abbreviation);
            $currency->setSymbol($symbol);

            //Et on hydrate la base
            $hydrateCurrency = \Prices\CurrencyManager::hydrateCurrency($db, $currency);
            if(is_a($hydrateCurrency, '\Exception')){
                throw new Exception($hydrateCurrency->getMessage(), $hydrateCurrency->getCode());
            }
        }
        $exchangeRate = $currency->getExchangeRate();
        //Si une donnée du taux de change a été modifiée
        if($exchangeRate->getRateToEuro() != $conversionToEuro){
            //On modifie en local
            $exchangeRate->setRateToEuro($conversionToEuro);
            $exchangeRate->setRateDate(new DateTime());

            //Et insert un nouveau
            // /!\ NOTICE : On en créé un nouveau afin d'avoir un historique des taux de change
            $insertExchangeRate = \Prices\ExchangeRateManager::insertExchangeRate($db, $exchangeRate, $currencyId);
            if(is_a($insertExchangeRate, '\Exception')){
                throw new Exception($insertExchangeRate->getMessage(), $insertExchangeRate->getCode());
            }
        }

        //On commit les changements
        $db->commit();
        $db = null;

        //Et message de succès
        $_SESSION['returnAction'] = array(1, 'Modification de la devise effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage());
        $db = null;

        return false;
    }
}