<?php

    namespace App\Controller;
    use App\Entity\Article;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
    use Symfony\Component\Form\Extension\Core\Type\DateType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\NumberType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Dompdf\Dompdf;
    use Dompdf\Options;
    

class IndexController  extends AbstractController{

    /**
     *@Route("/",name="article_list")
     */
    public function home()

    {
        
        $articles= $this->getDoctrine()->getRepository(Article::class)->findAll();
        return $this->render('articles/index.html.twig',array('articles'=> $articles));
    }

     

    
     

        /**
         * @Route("/article/new", name="new_article")
         * Method({"GET", "POST"})
         */
    public function new(Request $request) {
        $article = Article::getInstance();
        $form = $this->createFormBuilder($article)
        ->add('sexe',  ChoiceType::class, ['choices'  => ['Mr' => 'Mr', 'Mme' => 'Mme', ],])
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('numnat', TextType::class, array('label' => 'Numéro national'))
            ->add('societe', TextType::class)
            ->add('directeur', TextType::class)
            ->add('poste', ChoiceType::class, [
            'choices'  => ['Stagiaire' => 'Stagiaire','Alternant' => 'Alternant','Salarié' => 'Salarié',],])
            ->add('date_debut', DateType::class , ['format' => 'dd/MMM/yyyy','input' => 'string'])
            ->add('date_fin', DateType::class , ['format' => 'dd/MMM/yyyy','input' => 'string'])
            ->add('save', SubmitType::class, array('label' => 'Créer'))->getForm();
       
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
        
            return $this->redirectToRoute('article_list');
        }
        return $this->render('articles/new.html.twig',['form' => $form->createView()]);
    }



    /**
     * @Route("/article/{id}", name="article_show")
     */

    public function show($id) {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        return $this->render('articles/show.html.twig',array('article' => $article));
    }


    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id) {
        $article = Article::getInstance();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
    
        $form = $this->createFormBuilder($article)
        ->add('sexe',  ChoiceType::class, ['choices'  => [ 'Mr' => 'Mr','Mme' => 'Mme',],])
        ->add('nom', TextType::class)
        ->add('prenom', TextType::class)
        ->add('numnat', TextType::class, array('label' => 'Numéro national'))
        ->add('societe', TextType::class)
        ->add('directeur', TextType::class)
        ->add('poste', ChoiceType::class, ['choices'  => ['Stagiaire' => 'Stagiaire','Alternant' => 'Alternant', 'Salarié' => 'Salarié',],])
        ->add('date_debut', DateType::class , ['format' => 'dd/MMM/yyyy','input' => 'string',])
        ->add('date_fin', DateType::class , ['format' => 'dd/MMM/yyyy','input' => 'string'])
        ->add('save', SubmitType::class, array('label' => 'Modifier'))->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        
            return $this->redirectToRoute('article_list');
        }
        return $this->render('articles/edit.html.twig', ['form' => $form->createView(), array('article' => $article)]);
    }

    /**
     * @Route("/article/delete/{id}",name="delete_article")
     * @Method({"DELETE"})
     */
    
    public function delete(Request $request, $id) {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
    
        $response = new Response();
        $response->send();
        return $this->redirectToRoute('article_list');
    }

//Attestation de travail


    /**
     * @Route("/article/telecharger/{id}/attestaion_de_travail", name="telecharger_article")
     * Method({"GET", "POST"})
     */
    public function attestation_de_trvail_download(Request $request, $id)
    {

        $article = Article::getInstance();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
    
        $form = $this->createFormBuilder($article)
        ->add('sexe',  ChoiceType::class, [
            'choices'  => [
                'Mr' => 'Mr',
                'Mme' => 'Mme',
            ],])
        ->add('nom', TextType::class)
        ->add('prenom', TextType::class)
        ->add('numnat', TextType::class, array('label' => 'Numéro national'))
        ->add('societe', TextType::class)
        ->add('directeur', TextType::class)
        ->add('poste', ChoiceType::class, [
            'choices'  => [
                'Stagiaire' => 'Stagiaire',
                'Alternant' => 'Alternant',
                'Salarié' => 'Salarié',
            ],
        ])
        ->add('date_debut', DateType::class , ['format' => 'dd/MMM/yyyy','input' => 'string'])
        ->add('date_fin', DateType::class , ['format' => 'dd/MMM/yyyy','input' => 'string'])
        ->add('save', SubmitType::class, array('label' => 'Modifier'))->getForm();
    
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        
            return $this->redirectToRoute('article_list');
        }

        // configuration Dompdf par les options requis
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);


        // instanciation  Dompdf wavec ses options
        $dompdf = new Dompdf($pdfOptions);
        $date= date('d-m-y');
        // Récupérer le HTML généré dans notre fichier twig
        $html = $this->renderView('articles/attestation_de_trvail.html.twig', array('article' => $article ,'date' => $date,));
        
        // Charger HTML dans Dompdf
        $dompdf->loadHtml($html);
        
        //Configurez le format et l'orientation du papier "portrait" ou "portrait"
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le HTML au format PDF
        $dompdf->render();

        //Exporter le PDF généré vers le navigateur (forcer le téléchargement)        
        $dompdf->stream("attestaion_de_travail_$id.pdf", ["Attachment" => true]);
        exit(0);
    }


   

    /**
     * @Route("/article/voir_pdf/{id}/attestaion_de_travail")
     * Method({"GET", "POST"})
     */
    public function attestation_de_trvail_voir(Request $request, $id)
    {

        $article = Article::getInstance();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
    
        $form = $this->createFormBuilder($article)
        ->add('sexe',  ChoiceType::class, [
            'choices'  => [
                'Mr' => 'Mr',
                'Mme' => 'Mme',
            ],])
        ->add('nom', TextType::class)
        ->add('prenom', TextType::class)
        ->add('numnat', TextType::class, array('label' => 'Numéro national'))
        ->add('societe', TextType::class)
        ->add('directeur', TextType::class)
        ->add('poste', ChoiceType::class, [
            'choices'  => [
                'Stagiaire' => 'Stagiaire',
                'Alternant' => 'Alternant',
                'Salarié' => 'Salarié',
            ],
        ])
        ->add('date_debut', DateType::class , ['format' => 'dd/MMM/yyyy','input' => 'string'])
        ->add('date_fin', DateType::class , ['format' => 'dd/MMM/yyyy','input' => 'string'])
        ->add('save', SubmitType::class, array('label' => 'Modifier'))->getForm();
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        
            return $this->redirectToRoute('article_list');
        }

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $date= date('d-m-y');

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('articles/attestation_de_trvail.html.twig', array('article' => $article ,'date' => $date,) );
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("", array("Attachment" => 0));
    }






//Fiche de renseignement  


    /**
     * @Route("/article/telecharger/{id}/fiche_de_renseignement")
     * Method({"GET", "POST"})
     */
    public function fiche_renseignement_download(Request $request, $id)
    {

        $article = Article::getInstance();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
    
        $form = $this->createFormBuilder($article)
        ->add('sexe',  ChoiceType::class, [
            'choices'  => [
                'Mr' => 'Mr',
                'Mme' => 'Mme',
            ],])
        ->add('nom', TextType::class)
        ->add('prenom', TextType::class)
        ->add('numnat', TextType::class, array('label' => 'Numéro national'))
        ->add('societe', TextType::class)
        ->add('directeur', TextType::class)
        ->add('poste', ChoiceType::class, [
            'choices'  => [
                'Stagiaire' => 'Stagiaire',
                'Alternant' => 'Alternant',
                'Salarié' => 'Salarié',
            ],
        ])
        ->add('date_debut', DateType::class , ['format' => 'dd/MMM/yyyy','input' => 'string'])
        ->add('date_fin', DateType::class , ['format' => 'dd/MMM/yyyy','input' => 'string'])
        ->add('save', SubmitType::class, array('label' => 'Modifier'))->getForm();
    
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        
            return $this->redirectToRoute('article_list');
        }

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);


        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $date= date('d-m-y');
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('articles/fiche_renseignement.html.twig', array('article' => $article ,'date' => $date,));
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("fiche_de_renseignement_$id.pdf", ["Attachment" => true]);
        exit(0);
    }


   

    /**
     * @Route("/article/voir_pdf/{id}/fiche_de_renseignement", name="voir_article")
     * Method({"GET", "POST"})
     */
    public function fiche_renseignement_voir(Request $request, $id)
    {

        $article = Article::getInstance();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
    
        $form = $this->createFormBuilder($article)
        ->add('sexe',  ChoiceType::class, [
            'choices'  => [
                'Mr' => 'Mr',
                'Mme' => 'Mme',
            ],])
        ->add('nom', TextType::class)
        ->add('prenom', TextType::class)
        ->add('numnat', TextType::class, array('label' => 'Numéro national'))
        ->add('societe', TextType::class)
        ->add('directeur', TextType::class)
        ->add('poste', ChoiceType::class, [
            'choices'  => [
                'Stagiaire' => 'Stagiaire',
                'Alternant' => 'Alternant',
                'Salarié' => 'Salarié',
            ],
        ])
        ->add('date_debut', DateType::class , ['format' => 'dd/MMM/yyyy','input' => 'string'])
        ->add('date_fin', DateType::class , ['format' => 'dd/MMM/yyyy','input' => 'string'])
        ->add('save', SubmitType::class, array('label' => 'Modifier'))->getForm();
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        
            return $this->redirectToRoute('article_list');
        }

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $date= date('d-m-y');

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('articles/fiche_renseignement.html.twig', array('article' => $article ,'date' => $date,) );
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("", array("Attachment" => 0));
    }






    /**
     *@Route("articles/files",name="files_list")
     */
        public function files()

        {
            //récupérer tous les articles de la table article de la BD
            // et les mettre dans le tableau $articles
            $articles= $this->getDoctrine()->getRepository(Article::class)->findAll();
            return $this->render('articles/files.html.twig',array('articles'=> $articles));
        }


        


}