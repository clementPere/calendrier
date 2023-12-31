<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine, private CalendarRepository $calendar)
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', ["data" => $this->getAllRdvs()]);
    }


    #[Route('/{id}', name: 'app_event_edit', methods: 'PUT')]
    public function majEvent(?Calendar $calendar, Request $request): Response
    {

        $donnees = json_decode($request->getContent());

        if (
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) &&
            isset($donnees->borderColor) && !empty($donnees->borderColor) &&
            isset($donnees->textColor) && !empty($donnees->textColor)
        ) {
            $code = 200;
            if (!$calendar) {
                $calendar = new Calendar;
                $code = 201;
            }
            $calendar->setTitle($donnees->title);
            $calendar->setDescription($donnees->description);
            $calendar->setStart(new DateTime($donnees->start));
            if ($donnees->allDay) {
                $calendar->setEnd(new DateTime($donnees->start));
            } else {
                $calendar->setEnd(new DateTime($donnees->end));
            }
            $calendar->setAllDay($donnees->allDay);
            $calendar->setBackgroundColor($donnees->backgroundColor);
            $calendar->setBorderColor($donnees->borderColor);
            $calendar->setTextColor($donnees->textColor);

            $em = $this->doctrine->getManager();
            $em->persist($calendar);
            $em->flush();
            return new Response('OK', $code);
        } else {
            return new Response('Données incomplètes', 404);
        }
    }

    #[Route('/select/event', name: 'app_event_select')]
    public function selectEvent(?Calendar $calendar, Request $request, CalendarRepository $calendarRepository)
    {
        $donnees = json_decode($request->getContent());

        $calendar = new Calendar();
        $calendar->setStart(new DateTime($donnees->date));
        $calendar->setEnd(new DateTime($donnees->date));
        $calendar->setAllDay($donnees->allDay);
        $calendar->setDescription('ok');

        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            dd('Enregistrer !');
            $calendarRepository->save($calendar, true);
            $em = $this->doctrine->getManager();
            $em->persist($calendar);
            $em->flush();

            // return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('calendar/_form.html.twig', [
            "donnees" => $donnees,
            "form" => $form
        ]);
    }

    private function getAllRdvs()
    {
        $events = $this->calendar->findAll();

        $rdvs = [];
        foreach ($events as $event) {
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'allDay' => $event->isAllDay(),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
            ];
        }
        return json_encode($rdvs);
    }
}
