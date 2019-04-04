<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;

use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Doctrine\Common\Persistence\ObjectManager;


class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        $articles=$repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles'=>$articles
        ]);
    }
    /**
     * @route("/",name="home")
     */
    public function home(){
        return $this->render('blog/home.html.twig');

    }
    /**
     * @Route("/blog/new",name="blog_create")
     * @Route("/blog/{id}/edit",name="blog_edit")
     */
    public function form (Article $article=null,Request $request,ObjectManager $manager){

      if (!$article){$article=new Article();

      }

        $article->setTitle('titre d\'exemple')
                ->setContent('contenu de l\'article');

        //$form= $this->createFormBuilder($article)
        //    ->add('title')

          //  ->add('content')

            //->add('image')

           // ->getForm();
        $form=$this->createForm(ArticleType::class,$article);

        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){
            if (!$article->getId()){
                $article->setCreatedAt(new \DateTime());

            }
            $article->setCreatedAt(new \DateTime());
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show',['id'=>$article->getId()]);
        }


        return$this->render('blog/create.html.twig',
            ['formArticle' => $form->createView(),'editMode'=>$article->getId()!==null]);
    }
    /**
     * @route("/blog/{id}",name="blog_show")
     */
    public  function  show(Article $article,Request $request,ObjectManager $manager){
        $comment= new Comment();
       $form=$this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('blog_show',['id'=>$article->getId()]);
        }



        return $this->render('blog/show.html.twig',
            ['article'=>$article,'commentForm'=> $form->createView()]);

    }


}
