<?php

namespace App\Controller\Pages;

use App\Entity\File;
use App\Services\SelectFilesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserPanelController extends AbstractController
{
    #[Route('/user-panel',name:'app_user_panel')]
    public function userPanel(SelectFilesService $selectFilesService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if(!$user->getIsVerified())
        {
            $this->addFlash('EmailConfWarn','Before you access your file panel, please confirm your email address.');
            return $this->redirectToRoute('app_homepage');
        }
            
        
        
        return $this->render('pages/user_panel.html.twig',[
            'files'=>$selectFilesService->selectAllFiles($user),
        ]);
    }

    #[Route('/select-file',name:'app_select_file')]
    public function selectFile(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($request->isXmlHttpRequest())
        {
            $fileId = json_decode($request->request->get('id'));
            $file = $em->getRepository(File::class)->find($fileId);
            $fileResp = [
            "id"=>$file->getId(),
            "name"=>$file->getName(),
            "extension"=>$file->getExtension(),
            "size"=>$file->getSize(),
            "owner"=>$file->getOwner()->getEmail()
            ];
            return new JsonResponse(json_encode($fileResp));
        }
        else
            return $this->redirectToRoute('app_user_panel');
    }
    #[Route('/delete-file',name:'app_delete_file')]
    public function deleteFile(Request $request, EntityManagerInterface $em, Filesystem $filesystem): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($request->isXmlHttpRequest())
        {
            $fileId = json_decode($request->request->get('id'));
            $file = $em->getRepository(File::class)->find($fileId);
            $filePath = $this->getParameter("file_path")."/".$file->getName().".".$file->getExtension();
            $filesystem->remove($filePath);
            $em->remove($file);
            $em->flush();
            return new JsonResponse();
        }
        else
            return $this->redirectToRoute('app_user_panel');
    }
    #[Route('/rename-file',name:'app_rename_file')]
    public function renameFile(Request $request, EntityManagerInterface $em, Filesystem $filesystem): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($request->isXmlHttpRequest())
        {
            $fileId = json_decode($request->request->get('id'));
            $fileName = $request->request->get('name');
            $file = $em->getRepository(File::class)->find($fileId);
            $filePath = $this->getParameter("file_path")."/".$file->getName().".".$file->getExtension();
            $file->setName($fileName);
            $fileNewPath = $this->getParameter("file_path")."/".$file->getName().".".$file->getExtension();
            $filesystem->rename($filePath,$fileNewPath,true);
            $em->persist($file);
            $em->flush();
            return new JsonResponse($file->getExtension());
        }
        else
            return $this->redirectToRoute('app_user_panel');
    }


}

?>