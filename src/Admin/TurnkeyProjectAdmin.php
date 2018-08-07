<?php

namespace AppBundle\Admin;

use AppBundle\Entity\TurnkeyProject;
use AppBundle\Form\PurifiedTextareaType;
use AppBundle\Repository\TurnkeyProjectRepository;
use AppBundle\TurnkeyProject\TurnkeyProjectManager;
use League\Flysystem\Filesystem;
use League\Glide\Server;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TurnkeyProjectAdmin extends AbstractAdmin
{
    /**
     * @var Filesystem
     */
    private $storage;

    /**
     * @var Server
     */
    private $glide;

    /**
     * @var TurnkeyProjectManager
     */
    private $turnkeyProjectManager;

    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        TurnkeyProjectRepository $turnkeyProjecRepository,
        TurnkeyProjectManager $turnkeyProjectManager
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->turnkeyProjecRepository = $turnkeyProjecRepository;
        $this->turnkeyProjectManager = $turnkeyProjectManager;
    }

    protected $datagridValues = [
        '_page' => 1,
        '_per_page' => 32,
        '_sort_order' => 'DESC',
        '_sort_by' => 'position',
    ];

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('Projet clé en main', ['class' => 'col-md-7'])
                ->add('name', null, [
                    'label' => 'Nom',
                ])
                ->add('subtitle', null, [
                    'label' => 'Sous-titre',
                ])
                ->add('category', null, [
                    'label' => 'Catégorie',
                ])
                ->add('problemDescription', null, [
                    'label' => 'Description du problème',
                ])
                ->add('proposedSolution', PurifiedTextareaType::class, [
                    'label' => 'Solution du problème',
                ])
                ->add('requiredMeans', null, [
                    'label' => 'Feuille de route',
                ])
                ->add('image', null, [
                    'label' => 'Image d\'illustration',
                    'template' => 'admin/turnkey_project/show_image.html.twig',
                ])
                ->add('youtubeId', null, [
                    'label' => 'Youtube ID',
                ])
                ->add('isPinned', null, [
                    'label' => 'Épinglé',
                ])
                ->add('isFavorite', null, [
                    'label' => 'Mis en avant',
                ])
                ->add('position', null, [
                    'label' => 'Position',
                ])
            ->end()
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Projet clé en main', ['class' => 'col-md-7'])
                ->add('name', null, [
                    'label' => 'Nom',
                    'format_title_case' => true,
                ])
                ->add('subtitle', null, [
                    'label' => 'Sous-titre',
                    'format_title_case' => true,
                ])
                ->add('category', null, [
                    'required' => true,
                    'label' => 'Catégorie',
                ])
                ->add('problemDescription', null, [
                    'required' => true,
                    'label' => 'Description du problème',
                ])
                ->add('proposedSolution', PurifiedTextareaType::class, [
                    'label' => 'Solution du problème',
                    'filter_emojis' => true,
                    'purifier_type' => 'enrich_content',
                    'attr' => ['class' => 'ck-editor'],
                ])
                ->add('requiredMeans', null, [
                    'required' => true,
                    'label' => 'Feuille de route',
                    'filter_emojis' => true,
                ])
                ->add('image', FileType::class, [
                    'required' => false,
                    'label' => 'Ajoutez une image d\'illustration',
                ])
                ->add('youtubeId', TextType::class, [
                    'required' => false,
                    'label' => 'Youtube ID',
                    'help' => 'L\'ID de la vidéo Youtube ne peut contenir que des chiffres, des lettres, et les caractères "_" et "-"',
                    'filter_emojis' => true,
                ])
                ->add('isPinned', null, [
                    'label' => 'Épingler ce projet sur la page d\'accueil des Projets citoyens',
                ])
                ->add('isFavorite', null, [
                    'label' => 'Projet clé en main à mettre en avant',
                ])
                ->add('position', null, [
                    'label' => 'Position',
                ])
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, [
                'label' => 'ID',
            ])
            ->add('name', null, [
                'label' => 'Nom',
                'show_filter' => true,
            ])
            ->add('category', null, [
                'label' => 'Catégorie',
            ])
            ->add('isPinned', null, [
                'label' => 'Épinglé',
            ])
            ->add('isFavorite', null, [
                'label' => 'Mis en avant',
            ])
            ->add('position', null, [
                'label' => 'Position',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'template' => 'admin/turnkey_project/list_name.html.twig',
            ])
            ->add('category', null, [
                'label' => 'Catégorie',
            ])
            ->add('_image', null, [
                'label' => 'Miniature d\'image',
                'virtual_field' => true,
                'template' => 'admin/list/list_image_miniature.html.twig',
            ])
            ->add('youtubeId', null, [
                'label' => 'Youtube ID',
            ])
            ->add('isPinned', null, [
                'label' => 'Épinglé',
            ])
            ->add('isFavorite', null, [
                'label' => 'Mis en avant',
            ])
            ->add('position', null, [
                'label' => 'Position',
            ])
            ->add('_action', null, [
                'virtual_field' => true,
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
                'template' => 'admin/turnkey_project/list_actions.html.twig',
            ])
        ;
    }

    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $count = $this->turnkeyProjectManager->countProjects();
        $instance->setPosition(++$count);

        return $instance;
    }

    /**
     * @param TurnkeyProject $turnkeyProject
     */
    public function postRemove($turnkeyProject)
    {
        parent::postRemove($turnkeyProject);

        $this->turnkeyProjectManager->removeImage($turnkeyProject);
    }

    /**
     * @param TurnkeyProject $turnkeyProject
     */
    public function prePersist($turnkeyProject)
    {
        parent::prePersist($turnkeyProject);

        if ($turnkeyProject->getImage()) {
            $this->turnkeyProjectManager->saveImage($turnkeyProject);
        }
    }

    /**
     * @param TurnkeyProject $turnkeyProject
     */
    public function preUpdate($turnkeyProject)
    {
        parent::preUpdate($turnkeyProject);

        $em = $this->getModelManager()->getEntityManager($this->getClass());
        /** @var TurnkeyProject $oldTrunkeyProject */
        $oldTrunkeyProject = $em->getUnitOfWork()->getOriginalEntityData($turnkeyProject);

        if ($turnkeyProject->getImage()) {
            $this->turnkeyProjectManager->saveImage($turnkeyProject);
        }
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        if ($object->isPinned()) {
            $pinnedProject = $this->turnkeyProjecRepository->findPinned($object->getId());

            if ($pinnedProject) {
                $errorElement
                    ->with('isPinned')
                    ->addViolation(sprintf('Le projet clé en main "%s" est deja épinglé', $pinnedProject->getName()))
                    ->end()
                ;
            }
        }
    }

    public function setStorage(Filesystem $storage): void
    {
        $this->storage = $storage;
    }

    public function setGlide(Server $glide): void
    {
        $this->glide = $glide;
    }
}
