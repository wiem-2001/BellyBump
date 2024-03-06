<?php

namespace App\Controller;

use App\Entity\LikeDislike;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeDislikeController extends AbstractController
{
    #[Route('/like/{postId}/{value}', name: 'app_like_dislike')]
    public function index(ManagerRegistry $doctrine,PostRepository $postRepository,$postId,$value): Response
    {
        $value=$value=="true";
        $user=$this->getUser();
        $post=$postRepository->find($postId);

        $findLike=$doctrine->getManager()->getRepository(LikeDislike::class)->findOneBy(['User'=>$user,'Post'=>$post]);
        if($findLike){
            if($findLike->isValue()==$value){
                //delete
                $em=$doctrine->getManager();

                $em->remove($findLike);
                $em->flush();
            }else{
                $findLike->setValue(!$findLike->isValue());
                $em=$doctrine->getManager();
                $em->flush();
            }
        }else{

            $like=new LikeDislike();

            $like->setUser($user);
            $like->setPost($post);
            $like->setValue($value);
            $em=$doctrine->getManager();
            $em->persist($like);
            $em->flush();

        }$likeCounts = count($post->getLikeDislikes()->filter(function($like) { return $like->isValue() === true; }));
        $dislikeCounts = count($post->getLikeDislikes()->filter(function($like) { return $like->isValue() === false; }));
        return new JsonResponse([
            "likeCount"=>$likeCounts,
            "dislikeCount"=>$dislikeCounts
        ]);
    }
}
