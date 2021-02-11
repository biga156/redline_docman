<?php

namespace App\Controller;

use DateTimeZone;
//use App\Entity\Users;
use App\Entity\Documents;
use App\Form\DocumentsType;
use App\Form\DocumentsLimitedType;
use App\Repository\TagsRepository;
use App\Repository\FilesRepository;
use App\Repository\DocumentsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
//use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
//use Vich\UploaderBundle\Handler\DownloadHandler;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;



//* There are for EasyAdmin dashboard:
//use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\CrudControllerInterface;
//use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 *
 * @Route("/documents")
 */
class DocumentsController extends AbstractController
{
    /**
     * Note: all the docs of the active user (My documents)
     * @IsGranted("ROLE_USER_TEMP")
     * @Route("/", name="documents_index", methods={"GET"})
     */
    public function index(DocumentsRepository $documentsRepository, TagsRepository $tagsRepository, Request $request ): Response
    {   
        
        /*$form = $this->createForm(SelectType::class, $select);
        $form->handleRequest($request);*/
    if(isset($_GET['u'])){
        return $this->render('documents/index.html.twig', [
            'documents' => $documentsRepository->findDocumentsByUser(
                $_GET['u']
            ),
            'allTags' => $tagsRepository->findAll(),
            
            
        ]);
    }else{
        return $this->render('documents/index.html.twig', [
            'documents' => $documentsRepository->findDocumentsByUser(
                $this->getUser()
            ),
            'allTags' => $tagsRepository->findAll(),
            //'selectForm' => $form->createView(),
            
        ]);
    }
    }

    /**
     * Note: all public documents
     * @IsGranted("ROLE_USER")
     * @Route("/documents_public", name="documents_public", methods={"GET"})
     */
    public function indexPublic(
        DocumentsRepository $documentsRepository, TagsRepository $tagsRepository
    ): Response {
        return $this->render('documents/index.html.twig', [
            'documents' => $documentsRepository->findAllPublic(),
            'allTags' => $tagsRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/new", name="documents_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $document = new Documents();
        $form = $this->createForm(DocumentsType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
               $audioNoteFile = $form->get('audioNote')->getData();
            
            if ($audioNoteFile) {
                $originalFilename = pathinfo($audioNoteFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                
                $newFilename = $safeFilename.'-'.uniqid().'.'.$audioNoteFile->guessExtension();
                //$newFilename = $safeFilename.'-'.uniqid().'.'."wav";
                // Move the file to the directory where files are stored
                try {
                    $audioNoteFile->move(
                        $this->getParameter('audionote_directory'),
                        $newFilename
                    );
                    //$endFilename=$this->getParameter('audionote_directory')."/".$newFilename;
                } catch (FileException $e) {
                    
                }
                //dd($newFilename);
                $document->setAudioNote($newFilename);
            }

        
            $document->setCreatedAt(new \DateTime('now',  new DateTimeZone('Europe/Paris')));
            $document->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($document);
            $entityManager->flush();
            
            if(empty($form->get('audioNote')->getData())){
                return $this->redirectToRoute('files_new', ['doc'=>$document->getId()]);
                
            }else{
                //*test part
                return $this->redirectToRoute('files_new', ['doc'=>$document->getId()]);
               // return $this->redirectToRoute('files_new_audionote', ['doc'=>$document->getId(), 'an'=>$form->get('audioNote')->getData()]);
            }
      
       
            //return $this->redirectToRoute('files_new', ['doc'=>$document->getId()]);
        }

        return $this->render('documents/new.html.twig', [
            'document' => $document,
            'form' => $form->createView(),
        ]);
    }

      /**
       * NOTE: document creator for the non-verified users
       * @IsGranted("ROLE_USER_TEMP")
     * @Route("/new/limited", name="documents_limited_new", methods={"GET","POST"})
     */
    public function newLimited(Request $request): Response
    {
        $document = new Documents();
        $form = $this->createForm(DocumentsLimitedType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $document->setValidity(new \DateTime('now',  new DateTimeZone('Europe/Paris')));
            $document->setAlarm(new \DateTime('now-1 Days',  new DateTimeZone('Europe/Paris')));
            $document->setCreatedAt(new \DateTime('now',  new DateTimeZone('Europe/Paris')));
            $document->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($document);
            $entityManager->flush();

            // return $this->redirectToRoute('documents_index');
            return $this->redirectToRoute('files_new', ['doc'=>$document->getId()]);
        }

        return $this->render('documents/new.html.twig', [
            'document' => $document,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER_TEMP")
     * @Route("/{id}/show", name="documents_show", methods={"GET"})
     */
    public function show(Documents $document,  FilesRepository $filesRepository ): Response
    {
      
        return $this->render('documents/show.html.twig', [
            'document' => $document,
            'files' => $filesRepository->findAllFilesByDocument($document->getId()),
            
        ]);
    }

    /**
     * @Route("/{id}/edit", name="documents_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Documents $document): Response
    {
        $form = $this->createForm(DocumentsType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()
                ->getManager()
                ->flush();

            return $this->redirectToRoute('documents_index',['u'=>$this->getUser()->getId()]);
        }

        return $this->render('documents/edit.html.twig', [
            'document' => $document,
            'form' => $form->createView(),
        ]);
    }

     /**
     * Route("/{id}/addFile", name="documents_add_file", methods={"GET","POST"})
     */
   /* public function addFile(Request $request, Documents $document): Response
    {
        $form = $this->createForm(DocumentsType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()
                ->getManager()
                ->flush();

            return $this->redirectToRoute('files_new');
        }

        return $this->render('documents/edit.html.twig', [
            'document' => $document,
            'form' => $form->createView(),
        ]);
    }*/
    
    /**
     * @Route("/{id}/delete", name="documents_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Documents $document): Response
    {
        $filesystem=new Filesystem();
        if (
            $this->isCsrfTokenValid(
                'delete' . $document->getId(),
                $request->request->get('_token')
            )
        ) { 
            if($filesystem->exists($this->getParameter('audionote_directory')."/".$document->getAudioNote())){
            $audionote=$this->getParameter('audionote_directory')."/".$document->getAudioNote();
            $filesystem->remove($audionote);}

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($document);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('documents_index',['u'=>$this->getUser()->getId()]);
    }

     /**
     * @Route("/{id}/delete/public", name="documents_delete_pub", methods={"DELETE"})
     */
    public function deletePub(Request $request, Documents $document): Response
    {
        if (
            $this->isCsrfTokenValid(
                'delete' . $document->getId(),
                $request->request->get('_token')
            )
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($document);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('documents_public');
    }

   
    /**
     * 
     * @Route("/{id}/protected", name="documents_make_protected", methods={"ENABLE", "GET"})
     */
    public function makeProtected(Request $request, Documents $document): Response
    {
        if (
            $this->isCsrfTokenValid(
                'enableP' . $document->getId(),
                $request->request->get('_token')
            )
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $document->setProtection(true);
            $entityManager->persist($document);
            $entityManager->flush();
            $public=true;
        }else  if (
            $this->isCsrfTokenValid(
                'enable' . $document->getId(),
                $request->request->get('_token')
            )
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $document->setProtection(true);
            $entityManager->persist($document);
            $entityManager->flush();
            $public=false;
        }
        if($public){
            return $this->redirectToRoute('documents_public');
        }else{
         return $this->redirectToRoute('documents_index');
        }
    }

 /**
     * 
     * @Route("/{id}/unprotect", name="documents_make_unprotected", methods={"DISABLE"})
     */
    public function makeUnprotected(Request $request, Documents $document): Response
    {
        if (
            $this->isCsrfTokenValid(
                'disable' . $document->getId(),
                $request->request->get('_token')
            )
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $document->setProtection(false);
            $entityManager->persist($document);
            $entityManager->flush();
        }

        return $this->redirectToRoute('documents_index');
    }

     /**
     * @Route("/download/audio/{file}", name="download_audio_action")
     */
   /* public function downloadAction(
        File $file,
        DownloadHandler $downloadHandler
    ): Response {
        //return $downloadHandler->downloadObject($file, 'file');
        //NOTE: with options (quickview for example in browser):
        return $downloadHandler->downloadObject(
            $file,
            'file',
            $objectClass = null,
            $fileName = null,
            $forceDownload = false
        );
    }*/
    
}
