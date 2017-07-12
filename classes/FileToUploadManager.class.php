<?php
class FileToUploadManager{
    /**
     * @param FileToUpload $file
     *
     * @return array|Exception
     */
    public static function uploadFile(FileToUpload $file){
        try{
            $name = $file->getName();
            $tmpName = $file->getTmp_name();
            $error =  $file->getError();
            $size = $file->getSize();
            $pathOnServer = $file->getPathOnServer();
            $searchExt = explode('.', $name);
            $ext = $searchExt[count($searchExt) - 1];

            switch ($error) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new Exception('Aucun fichier envoyé.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception('Taille limite d\'un fichier atteinte.');
                default:
                    throw new Exception('Erreurs inconnues.');
            }

            if ($size > 10000000) {
                throw new Exception('Taille limite d\'un fichier atteinte.');
            }

            $uploadDir = $pathOnServer;
            $uploadFile = $uploadDir.'/'.$name;

            $dirs = explode('/', $uploadDir);
            foreach($dirs as $key => $dir){
                $dirToTest = '';
                for($i = 0; $i <= $key; $i++){
                    if($i > 0){
                        $dirToTest .= '/';
                    }
                    $dirToTest .= $dirs[$i];
                }
                if(!empty($dirToTest)){
                    if(!is_dir($dirToTest)){
                        mkdir($dirToTest);
                    }
                }
            }

            if(move_uploaded_file($tmpName, $uploadFile)){
                return array($uploadFile, $name);
            }
            else{
                throw new Exception('Impossible d\'uploader le fichier.');
            }
        }
        catch(Exception $e){
            return $e;
        }
    }
}