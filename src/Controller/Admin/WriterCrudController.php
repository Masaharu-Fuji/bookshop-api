<?php

namespace App\Controller\Admin;

use App\Entity\Writer;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class WriterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Writer::class;
    }
}
