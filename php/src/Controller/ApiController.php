<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CurrencyService;
use App\VO\CurrencyCode;
use DateMalformedStringException;
use DateTimeImmutable;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

class ApiController extends AbstractController {
    /**
     * @param Request $request
     * @param CurrencyService $currencyService
     * @return Response
     * @throws DateMalformedStringException
     */
    #[Route('/currencyOnDate', name: 'currencyOnDate')]
    public function getCurrencyOnDate(
        Request $request,
        CurrencyService $currencyService,
    ): Response {
        try {
            $date = new DateTimeImmutable($request->get(key: "date", default: "now"));
        } catch (Throwable) {
            return $this->json(
                data: ['error' => 'invalid date format: format must be YYYY-mm-dd'],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        try {
            $charCode = CurrencyCode::from($request->get(key: "charCode", default: "RUR"));
            $baseCharCode = CurrencyCode::from($request->get(key: "baseCharCode", default: "USD"));
        } catch (Throwable) {
            $cases = array_reduce(CurrencyCode::cases(), fn(string $carry,
                CurrencyCode $code) => "$carry, $code->name", '');
            $cases = trim($cases, ',');
            return $this->json(
                data: ['error' => "invalid currency code. Available: $cases"],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        try {
            $result = $currencyService->calculateCrossCurrencyOnDate(date: $date, code: $charCode, base: $baseCharCode);
            $prevDateResult = $currencyService->calculateCrossCurrencyOnDate(date: $date->modify('-1 day'), code: $charCode, base: $baseCharCode);
        } catch (TransportExceptionInterface) {
            return $this->json(['error' => 'internal error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'currency' => $result,
            'prevDay' => $prevDateResult,
        ]);
    }
}