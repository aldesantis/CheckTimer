<?php

namespace CheckTimer\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cleanup command
 *
 * Deletes all the expired, non-confirmed accounts and the expired email change
 * requests.
 *
 * @author Alessandro Desantis <desa.alessandro@gmail.com>
 */
class CleanupCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('checktimer:user:cleanup')
            ->setDescription('Clean up the users table.')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em   = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('CheckTimerUserBundle:User');

        $users = $repo->findAll();

        $notEnabled      = 0;
        $emailsDismissed = 0;
        $resetsDismissed = 0;

        foreach ($users as $user) {
            if (!$user->getEnabled()) {
                $diff = $user->getRegisteredAt()->diff(new \DateTime());

                if ($diff->format('%a') >= 1) {
                    $em->remove($user);
                    $notEnabled++;
                }

                continue;
            }

            if ($user->getNewEmail() != null) {
                $diff = $user->getEmailChangedAt()->diff(new \DateTime());

                if ($diff->format('%a') >= 1) {
                    $user->setNewEmail(null);
                    $user->setEmailChangedAt(null);
                    $emailsDismissed++;
                }
            }

            if ($user->getPasswordResetCode() != null) {
                $diff = $user->getPasswordResetAt()->diff(new \DateTime());

                if ($diff->format('%a') >= 1) {
                    $user->setPasswordResetCode(null);
                    $user->setPasswordResetAt(null);
                    $resetsDismissed++;
                }
            }
        }

        $em->flush();

        $output->writeln(sprintf('<info>%d</info> not enabled accounts deleted.', $notEnabled));
        $output->writeln(sprintf('<info>%d</info> email change requests deleted.', $emailsDismissed));
        $output->writeln(sprintf('<info>%d</info> password reset requests deleted.', $resetsDismissed));
    }
}
