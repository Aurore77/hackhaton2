<?php

namespace CatchmeBundle\Controller;

use CatchmeBundle\Entity\Challenge;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Challenge controller.
 *
 */
class ChallengeController extends Controller
{
    /**
     * Lists all challenge entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $challenges = $em->getRepository('CatchmeBundle:Challenge')->findAll();

        return $this->render('CatchmeBundle:user/challenge:index.html.twig', array(
            'challenges' => $challenges,
        ));
    }

    /**
     * Creates a new challenge entity.
     *
     */
    public function newAction(Request $request)
    {
        $challenge = new Challenge();
        $form = $this->createForm('CatchmeBundle\Form\ChallengeType', $challenge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($challenge);
            $em->flush($challenge);

            return $this->redirectToRoute('challenge_show', array('id' => $challenge->getId()));
        }

        return $this->render('@Catchme/user/challenge/new.html.twig', array(
            'challenge' => $challenge,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a challenge entity.
     *
     */
    public function showAction(Challenge $challenge)
    {
        $deleteForm = $this->createDeleteForm($challenge);

        return $this->render('@Catchme/user/challenge/show.html.twig', array(
            'challenge' => $challenge,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing challenge entity.
     *
     */
    public function editAction(Request $request, Challenge $challenge)
    {
        $deleteForm = $this->createDeleteForm($challenge);
        $image = $em->getRepository('CatchmeBundle:Image')->findOneById($challenge->getImage()->getId());

        $editForm = $this->createForm('CatchmeBundle\Form\ChallengeType', $challenge);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $image->preUpload();
            $em->persist($challenge);
            $em->flush();

            return $this->redirectToRoute('challenge_edit', array(
                'id' => $challenge->getId()
            ));
        }

        return $this->render('@Catchme/user/challenge/edit.html.twig', array(
            'challenge' => $challenge,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a challenge entity.
     *
     */

    public function deleteAction($id)
    {
//        Si l'$id est définie alors :
        if ($id) {
            $em = $this->getDoctrine()->getManager();
            // Recherche L'ARTICLE à supprimer parmi LES ARTICLES
            $article = $em->getRepository('CatchmeBundle:Challenge')->findOneById($id);
            // Recherche L'IMAGE DE L'ARTICLE visé
            $image = $em->getRepository('CatchmeBundle:Image')->findOneById($article->getImage()->getId());
            // Supprime L'ARTICLE et SON IMAGE associée
            $em->remove($challenge);
            $em->remove($image);
            // Envoie la requête à la BDD
            $em->flush();
            return $this->redirectToRoute('challenge_show');
        } else
            return $this->redirectToRoute('challenge_show');
    }
}
