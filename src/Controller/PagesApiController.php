<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PagesApiController extends AbstractController
{

    /**
     * @Route("/pages/", name="pages")
     */
    public function index()
    {
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/pages/{id}}", name="pages_id")
     */
    public function getUserInfo($id)
    {
        //получаем ключи приложения которые находятся в .env.local
        $app_id = $_ENV['FB_APP_ID'];
        $app_secret = $_ENV['FB_APP_SECRET'];

        //Получаем AcessToken для приложения
        $curl = curl_init('https://graph.facebook.com/oauth/access_token?client_id='.$app_id.'&client_secret='.$app_secret.'&grant_type=client_credentials');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $json = json_decode(curl_exec($curl));
        curl_close($curl);
            
        $fb = new Facebook([
          'app_id' => $app_id,           
          'app_secret' => $app_secret,   
          'graph_api_version' => 'v5.0',
        ]);
          
        //echo  typeof($json->{'access_token'});
        //пробуем получить информацию о пользователе
        try {   
        // Get your UserNode object, replace {access-token} with your token
          $response = $fb->get('/'.$id.'?fields=id,name,first_name,last_name,name_format', $json->{'access_token'});
        } catch(FacebookResponseException $e) {
          // Returns Graph API errors when they occur
          return $this->json([
            'error' => 'Graph returned an error: '. $e->getMessage(),
          ]);
        } catch(FacebookSDKException $e) {
          // Returns SDK errors when validation fails or other local issues
          return $this->json([
            'error' => 'Facebook SDK returned an error: '. $e->getMessage(),
          ]);
        }

        //получаем информацию о пользователе
        $userInfo = $response->getGraphUser();

        //выдаем json объект
        return $this->json([
            'name' => $userInfo->getName(),
            'name_format' => $userInfo->getProperty('name_format'),
            'first_name' => $userInfo->getFirstName(),
            'last_name' => $userInfo->getLastName(),
            'id' => $userInfo->getId(),
        ]);

        //либо можно было просто выкинуть весь объект который вернул фейсбук
        //return $this->json(json_decode($userInfo));
    }
}
