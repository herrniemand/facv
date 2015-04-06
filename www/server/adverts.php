<?php
    // UNCOMMENT FOR DEBUG
    error_reporting(E_ALL); 
    ini_set( 'display_errors','1');

    //Set header to JSON
    header('Content-Type: application/json');
    
    $maxRequestPerDay = 5;

    //Add SQL methods
    include __DIR__.'/includes/sql_requests.php';

    /* Initializing new SQLGet and SQLSet objects. */
    $SQLGet = new SQLRequests\Get();
    $SQLAdd = new SQLRequests\Add();
    
    if (!$_POST) { //IF GET
        

        //Getting info
        $content = array(
            'status'     =>  200,
            'adverts'    =>  $SQLGet -> adverts()
        );

        //Returning JSON
        echo json_encode($content);

    }else{ 
    //IF POST
        include __DIR__.'/includes/ip.php';
        include __DIR__.'/includes/validate.php';
        include __DIR__.'/includes/upload.php';
        
        $upload = new \Upload\Upload();
        $IP = new IP();

        $ValidatePOST = new Validate\POST();
        $ValidateRESP = $ValidatePOST -> advert();

        if( $ValidateRESP['valid'] ){

            //Set gets response
            if($SQLGet -> ip($IP -> get()) < $maxRequestPerDay){
                
                //Add ip to DB
                $SQLAdd -> ip($IP -> get());

                $image = '';

                if(isset($_POST['image'])){
                    $image = $upload -> upload($_POST['image']);
                }

                $responce = $SQLAdd -> advert($image);

                if($responce['status'] === 200){
                    $responce['advert'] = $SQLGet -> advert($responce['id'])[0];
                }

                echo json_encode( $responce );
                
            }else{

                echo json_encode( array( 'status' => 429, 'errorMessage' => 'You have reached maximum of your advert per day' ) );

            }

        }else{
            echo json_encode( array( 'status' => 412, 'errorMessage' => $ValidateRESP['messages'] ) );
        }
    }
?>