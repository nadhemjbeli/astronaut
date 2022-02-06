<?php


namespace App\Controller;


use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\MarkdownHelper;
use App\Service\SlackClient;
use Doctrine\ORM\EntityManagerInterface;
use Michelf\MarkdownInterface;
use Nexy\Slack\Client;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ArticleController extends AbstractController
{
    /**
     * Currently unused: just showing a controller with constructor!
     */
    private $isDebug;

    public function __construct(bool $isDebug){

        $this->isDebug = $isDebug;
    }

    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(ArticleRepository $repository)
    {
        $articles = $repository->findAllPublishedOrderedByNewest();


        return $this->render('article/homepage.html.twig',[
            'articles' => $articles
        ]);
    }
    /**
     * @Route("/news/{slug}", name="article_show")
     */
    public function show(Article $article /*, MarkdownHelper $markdownHelper, SlackClient $slack*/)
    {

//        if($article->getSlug() == 'aa'){
//            $slack->sendMessage('nadhem', 'ah kirk, my old friend...');
//        }




        $comments = [
            'I ate a normal rock once. It did NOT taste like bacon!',
            'Woohoo! I\'m going on an all-asteroid diet!',
            'I like bacon too! Buy some from my site! bakinsomebacon.com',
        ];

//        dump($slug, $this);


        return $this->render('article/show.html.twig', [
            'article'=>$article,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/news/{slug}/heart", name="article_toggle_heart", methods={"POST"})
     */
    public function toggleArticleHeart(Article $article, LoggerInterface $logger, EntityManagerInterface $em){

        $article->incrementHeartCount();
        $em->flush();//persist is unnecessary for updates
        $logger->info('Article is being hearted');
        return new JsonResponse(['hearts'=> $article->getHeartCount()]);
    }
}