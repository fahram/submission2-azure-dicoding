<?php
require_once 'vendor/autoload.php';
error_reporting(-1);
ini_set('display_errors', 'On');

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$ACCOUNT_NAME = "dicodings";
$ACCOUNT_KEY = "rIHEJnXYjqPgTIgyoX/E5jojp1RUkBmhGHVC+751JUdeANNP/JbCGMeZs629SMQZIuQpj2gvN9ZD8MD0J9QjjQ==";

$connectionString = "DefaultEndpointsProtocol=https;AccountName=$ACCOUNT_NAME;AccountKey=$ACCOUNT_KEY";

// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
$containerName = "image";
$listBlobsOptions = new ListBlobsOptions();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
$target_dir = "uploads/";
$fileToUpload = $target_dir . basename($_FILES["fileToUpload"]["name"]);

    if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $fileToUpload)){
        try {
            $content = fopen($fileToUpload, "r");     
            $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
        }
        catch(ServiceException $e){
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
        catch(InvalidArgumentTypeException $e){
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
    }
}

?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dicoding Azure Submission</title>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <div class="card">
                    <div class="card-header">List of file from blob storage</div>
                    <div class="card-body">
                        <?php 

                        do {
                            $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
                            foreach ($result->getBlobs() as $blob) {
                                echo '<a href="#" data="'.$blob->getName().'" class="imageslct"><img src="'.$blob->getUrl().'" class="mr-3" alt="..." height="80px" class="img-thumbnail" title="'.$blob->getName().'"></a>';
                            }

                            $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                        } while ($result->getContinuationToken());

                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <p class="card-text">info (please select image)</p>
                    </div>
                </div>
            </div>

            
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mt-4">
                
                <div class="card">
                    <div class="card-header">Upload Image</div>
                    <div class="card-body">

                    <form method="post"  enctype="multipart/form-data">
                        <div class="form-group">
                          <label for="image"></label>
                          <input type="file" class="form-control-file" name="fileToUpload" id="fileToUpload" placeholder="Select Image" aria-describedby="fileHelpId">
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>

                    
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</body>
</html>