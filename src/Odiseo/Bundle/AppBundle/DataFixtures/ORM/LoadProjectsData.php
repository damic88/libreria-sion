<?php

namespace Odiseo\Bundle\AppBundle\DataFixtures\ORM;

use Odiseo\Bundle\AppBundle\Entity\Project;
use Odiseo\Bundle\AppBundle\Entity\ProjectFile;
use Odiseo\Bundle\AppBundle\Entity\ProjectImage;
use Odiseo\Bundle\AppBundle\Entity\Tag;
use Symfony\Component\Finder\Finder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadProjectsData extends AbstractFixture implements OrderedFixtureInterface
{
    protected $tags = ['muestras', 'archivos', 'proyectos'];

    public function load(ObjectManager $manager)
    {
        $tags = array();

        foreach($this->tags as $tagName)
        {
            $tag = new Tag();
            $tag->setName($tagName);

            $manager->persist($tag);

            $tags[] = $tag;
        }

        $imageIndex = 1;
        $fileIndex = 1;
        for($i=1; $i<=20;$i++)
        {
            $project = new Project();
            $project->setName('Proyecto '.$i);
            $project->setDescription('Ut vel ex eu ipsum egestas feugiat in ac nibh. Etiam consectetur quam quis sapien vulputate, ac efficitur ligula pharetra. Cras in nibh sed ipsum maximus convallis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec id venenatis nulla. Donec ullamcorper porta lacus et auctor. Praesent convallis augue viverra ultrices aliquet.<br>Integer auctor, dolor eget consequat hendrerit, arcu ex egestas ex, vitae malesuada urna ipsum at nulla. Ut laoreet, ligula id semper ultrices, risus purus convallis nibh, nec faucibus eros metus in ipsum. Maecenas non urna eget nisl fermentum lobortis. Proin a ipsum congue, dignissim lectus sit amet, luctus tortor. Sed ullamcorper diam non sagittis ultricies. Praesent rutrum, massa eu porttitor hendrerit, purus purus posuere augue, a aliquet leo augue congue diam. Phasellus et eleifend lectus, vitae blandit nibh. Praesent a dolor id mauris eleifend convallis. Etiam eget nulla ut nibh vulputate tempus. Nulla facilisi. Etiam accumsan libero at laoreet mollis.');

            $imageFinder = new Finder();
            $imagesPath = __DIR__.'/../../Resources/fixtures/project_images';

            $projectImage = new ProjectImage();

            foreach ($imageFinder->files()->in($imagesPath)->name('project'.$imageIndex.'.jpg') as $img)
            {
                $file = new UploadedFile($img->getRealPath(), $img->getFilename());

                $projectImage->setImageFile($file);
            }

            $projectImage->setProject($project);
            $projectImage->setIsMain(true);
            $manager->persist($projectImage);
            $project->addImage($projectImage);

            $filesPath = __DIR__.'/../../Resources/fixtures/project_files';
            $fileFinder = new Finder();

            foreach ($fileFinder->files()->in($filesPath) as $fileItem)
            {
                /** @var ProjectFile $projectFile */
                $projectFile = new ProjectFile();

                $file = new UploadedFile($fileItem->getRealPath(), $fileItem->getFilename());

                $projectFile->setFile($file);
                $projectFile->setProject($project);
                $projectFile->setPosition(0);
                $manager->persist($projectFile);
                $project->addFile($projectFile);
            }


            //$project->addTag($tags[array_rand(array_keys($tags))]);
            $manager->persist($project);

            $imageIndex++;
            if($imageIndex == 6) $imageIndex = 1;
        }
    	
    	$manager->flush();
    }

    public function getOrder()
    {
    	return 2;
    }
}