<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\Writer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'app_api_login')]
    public function index(
        #[CurrentUser()] ?Writer $writer,
        EntityManagerInterface $entityManager,
        Request $request,
    ): JsonResponse {
        if (null === $writer) {
            return $this->json([
                'message' => 'missing credentials',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $token = new ApiToken($writer);
        $entityManager->persist($token);
        $entityManager->flush();

        /*
        $header = base64_encode('{
            "alg":"HS256","typ":"JWT"
        }');
        $payload = base64_encode('{
            "sub": "1234567890",
            "name": "John Doe",
            "iat": 1516239022
        }');
        //署名なしTokenに対し、秘密鍵とHMAC-SHA256を用いて署名を生成
        $adaptation = ('{"alg":"HS256","typ":"JWT"}');
        $signature = base64_encode($adaptation);

        $jwtToken = "$header.$payload.$signature";
 */
        return $this->json([
            'user'  => $writer->getUserIdentifier(),
            'token' => $token->getToken(), //ApiTokenEntityに追加する感じで・
            'tokenWriter' => $token->getWriter(),
            'header_token' => $request->headers->get('Authorization'),
        ]);
    }
}
