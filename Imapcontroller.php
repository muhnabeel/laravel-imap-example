<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Companies;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Mail;
use DB;
use Config;


class AdminController extends Controller
{


   public function compose_mail(Request $request){
 

 $settings = DB::table('account_settings')->get();
    
 




     $validated = $request->validate([
        'to' => 'required',
        'emailSubject' => 'required',
    ]);

  
   Config::set('mail.host', $settings[0]->host_name);
   Config::set('mail.username', $settings[0]->user_name);
   Config::set('mail.password', $settings[0]->password);
   Config::set('mail.port', $settings[0]->outgoing_port);
   Config::set('mail.encryption', $settings[0]->outgoing_encryption);




$fromemail = $settings[0]->from_email;
 
 $fromname = $settings[0]->from_name;

    $to = $request->input('to');
    $emailSubject = $request->input('emailSubject');
    $messages = $request->input('message');


 try {
       $data = array('name'=>'dummy@gmail.com','messages'=> $messages ,'city'=>'karachi');
   
      Mail::send('mail', $data, function($message) use ($to,$emailSubject,$fromemail,$fromname) {
         $message->to($to)->subject($emailSubject);
          $message->from($fromemail, $fromname);
         

        
      });

       return redirect()->back()->with('success_message', 'Email  Successfully Sent!');
       
   } catch (Exception $ex) {

     return redirect()->back()->with('error_message', 'Something Went Wrong!');
            
      
    }       

  }

  // Email App
  public function emailApp()
  {
 



 $settings = DB::table('account_settings')->get();
    
 

 // $_SESSION['email'] = $settings[0]->user_name;
 // $_SESSION['password'] = $settings[0]->password;
 // $_SESSION['host'] = $settings[0]->host_name;
 // $_SESSION['type'] = $settings[0]->type;
 // $_SESSION['outgoing_port'] = $settings[0]->outgoing_port;




$cm = new ClientManager();

// or use an array of options instead




$client = $cm->make([

    'host'          => $settings[0]->host_name,
   'port'          => $settings[0]->incoming_port,
   'encryption'    => $settings[0]->incoming_encryption,
   'validate_cert' => true,
   'protocol'      => $settings[0]->type,
   'username'      => $settings[0]->user_name,
   'password'      => $settings[0]->password,
   'options' => [
        'fetch_order' => 'desc',
    ]


]);


$client->connect();



// foreach ($aMessageSentMail as $key => $value) {
  
//   echo "<Pre>";
//   print_r($value);
//   echo "</Pre>";

//   # code...
// }


/** @var \Webklex\PHPIMAP\Client $client */

// or create a new instance manually

//Connect to the IMAP Server


//Get all Mailboxes
/** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
//$folders = $client->getFolders();



 
 
 $oFolder = $client->getFolder('INBOX');

 $aMessage = $oFolder->query()->all()->get();

// $paginator = $aMessage->paginate($per_page = 5);

 //$aMessage = $oFolder->query()->since(now()->subDays(1))->get();

  
  $paginator = $aMessage->paginate($per_page = 5, $page = null, $page_name = 'page');





 // echo "<pre>";
 // print_r($paginator);
 // echo "</pre>";




//Loop through every Mailbox
/** @var \Webklex\PHPIMAP\Folder $folder */
// foreach($folders as $folder){

//     //Get all Messages of the current Mailbox $folder
//     /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
//     $messages = $folder->messages()->all()->get();
    
//     /** @var \Webklex\PHPIMAP\Message $message */
//     foreach($messages as $message){
//         echo $message->getSubject().'<br />';
//         echo 'Attachments: '.$message->getAttachments()->count().'<br />';
//         echo $message->getHTMLBody();
        
//         //Move the current Message to 'INBOX.read'
//         if($message->move('INBOX.read') == true){
//             echo 'Message has ben moved';
//         }else{
//             echo 'Message could not be moved';
//         }
//     }

// }    

    $pageConfigs = [
      'pageHeader' => false,
      'contentLayout' => "content-left-sidebar",
      'pageClass' => 'email-application',
    ];

    return view('admin.email.email', ['pageConfigs' => $pageConfigs , 'paginator' => $paginator  ]);
  }  



   public function sentemailApp()
  {
 



    $settings = DB::table('account_settings')->get();
 
    $cm = new ClientManager();

    // or use an array of options instead
    $client = $cm->make([

      'host'          => $settings[0]->host_name,
     'port'          => $settings[0]->incoming_port,
     'encryption'    => $settings[0]->incoming_encryption,
     'validate_cert' => true,
     'protocol'      => $settings[0]->type,
     'username'      => $settings[0]->user_name,
     'password'      => $settings[0]->password,
     'options' => [
          'fetch_order' => 'desc',
      ]


    ]);


    $client->connect();


    $folders = $client->getFolders();

    $SentMail = $client->getFolder('Sent');

     $aMessageSentMail = $SentMail->query()->all()->get();

     // $sentpaginator = $aMessageSentMail->paginate();

     $sentpaginator = $aMessageSentMail->paginate();



    $pageConfigs = [
      'pageHeader' => false,
      'contentLayout' => "content-left-sidebar",
      'pageClass' => 'email-application',
    ];

    return view('admin.email.sentemail', ['pageConfigs' => $pageConfigs , 'sentpaginator' => $sentpaginator ]);
 

  } 





}
