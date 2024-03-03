<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordUpdateFormType;
use App\Form\UpdateProfilFormType;
use App\Repository\UserRepository;
use App\Service\Dbscan;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;


class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('baseBackOffice.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/getAll_users', name: 'get_users')]
    public function getUsers(UserRepository $repository): Response
    {
        $users = $repository->findUsersWithMotherRole();
        return $this->render('user/users.html.twig', array('users' => $users));
    }

    #[Route('/user/delete/{id}', name: 'delete_user')]
    public function delete(ManagerRegistry $managerRegistry, $id, UserRepository $repository, MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $repository->find($id);
        $em = $managerRegistry->getManager();

        if ($user && in_array('ROLE_MOTHER', $user->getRoles(), true)) {
            // Sending email to the mother
            $email = (new TemplatedEmail())
                ->from(new Address('bellybump4@gmail.com', 'BellyBump account Status'))
                ->to($user->getEmail())
                ->subject('Account Deletion Notification')
                ->htmlTemplate('user/userDeletionEmailTemplate.php');
            $mailer->send($email);
            // Remove the user
            $em->remove($user);
            $em->flush();
        } else {
            return new Response("Error");
        }

        return $this->redirectToRoute('get_users');
    }

    #[Route('/user/details/{id}', name: 'detail_user')]
    public function showProfil($id, UserRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MOTHER');
        $user = $repository->find($id);
        $form = $this->createForm(UpdateProfilFormType::class, $user);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('user/show_profil.html.twig', [
            'user' => $user,
            'profilUpdateForm' => $form->createView(),
        ]);
    }

    #[Route('/update-password', name: 'update_password')]
    public function updatePassword(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MOTHER');

        // Get the current user
        $user = $this->getUser();

        // Handle the password update form submission
        $form = $this->createForm(PasswordUpdateFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the new password from the form
            $newPassword = $form->get('plainPassword')->getData();

            // Update the user's password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $newPassword
                )
            );

            // Persist the changes to the database
            $em = $managerRegistry->getManager();
            $em->flush();

            // Add success flash message
            $this->addFlash('success', 'Your password has been successfully updated.');

            // Redirect the user after successful password update
            return $this->redirectToRoute('update_password');
        }

        // Render the password update form
        return $this->render('user/update_password.html.twig', [
            'passwordUpdateForm' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/updateProfil', name: 'update_profil')]
    public function updateProfil(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MOTHER');
        // Get the current user
        $user = $this->getUser();
        // Handle the profile update form submission
        $form = $this->createForm(UpdateProfilFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the updated profile data from the form
            $user = $form->getData();
            $imageFile = $form->get('image')->getData();
            if ($imageFile instanceof UploadedFile) {
                // Generate a unique name for the file before saving it
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where images are stored
                $imageFile->move(
                    $this->getParameter('images_directory_user'),
                    $newFilename
                );
            }
            // Update the 'image' property to store the image file name
            $user->setImage($newFilename);
            // Persist the changes to the database
            $em = $managerRegistry->getManager();
            $em->persist($user);
            $em->flush();

            // Redirect the user after successful profile update
            return $this->redirectToRoute('detail_user', ['id' => $user->getId()]);
        }

        // Render the profile update form
        return $this->render('user/show_profil.html.twig', [
            'profilUpdateForm' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/user/updateStatus/{id}', name: 'update_status')]
    public function updateStatus(ManagerRegistry $managerRegistry, $id, UserRepository $repository, MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $repository->find($id);
        $em = $managerRegistry->getManager();

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Toggle user status
        if($user->isStatus()==1)
        {
            $user->setStatus(0);
        }
        else{
            $user->setStatus(1);

        }
        // Save the changes
        $em->flush();

        // Sending email to the user if the status is updated
        $email = (new TemplatedEmail())
            ->from(new Address('bellybump4@gmail.com', 'BellyBump account Status'))
            ->to($user->getEmail())
            ->subject('Account Status Update Notification')
            ->htmlTemplate('user/userStatusUpdateEmailTemplate.php')
            ->context([
                'status' => $user->isStatus(), // Pass the user's status to the template
            ]);

        $mailer->send($email);

        return $this->redirectToRoute('get_users');
    }
    #[Route('/stats', name: 'user_stats')]
    public function stats(UserRepository $userRepository, ChartBuilderInterface $chartBuilder): Response
    {
        // Get all users registered this year
        $currentYear = (new \DateTime())->format('Y');
        $users = $userRepository->findUsersWithMotherRole();
        $blockedUsersCount = $userRepository->countBlockedUsers();
        $unblockedUsersCount = $userRepository->countunBlockedUsers();

        $labels = [];
        $datasets = [];

        // Initialize an array to hold user counts for each month
        $userCountsByMonth = [
            '01' => 0, '02' => 0, '03' => 0, '04' => 0,
            '05' => 0, '06' => 0, '07' => 0, '08' => 0,
            '09' => 0, '10' => 0, '11' => 0, '12' => 0
        ];

        // Count users for each month
        foreach ($users as $user) {
            $createdAt = $user->getCreatedAt();
            if ($createdAt !== null ) { // Check if createdAt is not null
                $year = $createdAt->format('Y');
                if ($year == $currentYear) { // Check if the user was created in the current year
                    $monthNumber = $createdAt->format('m'); // Extract month number
                    $monthNumber = sprintf('%02d', $monthNumber); // Format month number to ensure two digits
                    if (array_key_exists($monthNumber, $userCountsByMonth)) {
                        $userCountsByMonth[$monthNumber]++;
                    } else {
                        // Handle unexpected month numbers if necessary
                    }
                }
            }
        }

        // Generate labels for each month
        $monthNames = [
            '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
            '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
            '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];

        // Modify $userCountsByMonth array to use month names as labels
        $userCountsByMonthWithNames = [];
        foreach ($userCountsByMonth as $monthNumber => $userCount) {
            $monthName = $monthNames[$monthNumber];
            $userCountsByMonthWithNames[$monthName] = $userCount;
        }

        // Generate dataset
        $datasets[] = [
            'label' => 'New Users',
            'data' => array_values($userCountsByMonth), // Values of user counts for each month
            'backgroundColor' => 'rgb(255, 99, 132)',
            'borderColor' => 'rgb(255, 99, 132)',
            'borderWidth' => 1
        ];

        // Create the bar chart
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => array_keys($userCountsByMonthWithNames),
            'datasets' => $datasets,
        ]);

        return $this->render('user/userStats.html.twig', [
            'chart' => $chart,
            'userCountsByMonth' => $userCountsByMonthWithNames,
            'blockedUsersCount' => $blockedUsersCount,
            'unblockedUsersCount'=>$unblockedUsersCount
        ]);
    }
    #[Route('/test-dbscan-connection', name: 'test_dbscan_connection')]
    public function testDbscanConnection(Dbscan $dbscanService): Response
    {
        // Sample data to test DBSCAN connection
        $testData = [20, 25, 27, 30, 33, 35, 40, 42, 45, 50];

        try {
            // Call the performClustering method to test the connection
            $result = $dbscanService->performClustering($testData);

            // Output the result (for testing purposes)
          //  dd($result); // Use dd() to dump and die (halt execution) for debugging

            // Optionally, return a response confirming successful connection
            return new Response('Connection to Python script successful!');
        } catch (\Exception $e) {
            // Handle any exceptions that may occur (e.g., connection error)
            return new Response('Connection to Python script failed: ' . $e->getMessage());
        }
    }

}
