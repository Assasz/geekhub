<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use AppBundle\Entity\SearchActivity;

class SearchActivityRecorder
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function record($input, User $user)
    {
        $input = explode(' ', $input);

        $tags = $this->em->getRepository(Tag::class)
            ->findByName($input);

        foreach($tags as $tag)
        {
            $searchActivity = $user->getSearchActivity()->toArray();

            $activityTags = [];

            foreach($searchActivity as $activity)
            {
                $activityTags[] = $activity->getTag();
            }

            if(!in_array($tag, $activityTags))
            {
                $newSearchActivity = new SearchActivity();
                $newSearchActivity->setUser($user);
                $newSearchActivity->setTag($tag);

                if(count($searchActivity) > 9)
                {
                    $lastActivity = $this->em->getRepository(SearchActivity::class)
                        ->findLast($user);

                    $user->removeSearchActivity($lastActivity);
                    $this->em->remove($lastActivity);
                }

                $user->addSearchActivity($newSearchActivity);
                $this->em->persist($newSearchActivity);
            }
            else
            {
                $activity = $this->em->getRepository(SearchActivity::class)->findOneBy([
                    'tag' => $tag,
                    'user' => $user
                ]);

                $activity->setSearchDate(new \DateTime('now'));
            }
        }

        $this->em->flush();
    }
}
