<?php

namespace App\Service;

use App\Entity\Event;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExportService extends AbstractController
{
    public function __construct()
    {}

    /**
     * @param string $name
     * @param Event $event
     * @param array $books
     */
    public function export(
        string $name,
        Event $event,
        array $books
    ): void
    {
        $options = new Options();
        $options->set('defaultFont', 'Roboto');

        $dompdf = new Dompdf($options);

        $html = $this->render('event/book.html.twig', [
            'event' => $event,
            'books' => $books,
            'eventImage' => $this->imageToBase64($this->getParameter('project_root') . $event->getImagePath()),
            'quoteImage' => $this->imageToBase64($this->getParameter('project_root') . '/images/quote.png'),
            'logoKuuwaa' => $this->imageToBase64($this->getParameter('project_root') . '/images/logo.png'),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream($name, [
            "Attachment" => true
        ]);
    }

    public function imageToBase64($path): string
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}