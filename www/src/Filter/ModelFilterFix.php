<?php


namespace App\Filter;


use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\CoreBundle\Form\Type\EqualType;
use Sonata\DoctrineMongoDBAdminBundle\Filter\ModelFilter;

class ModelFilterFix extends ModelFilter
{
    protected static function fixIdentifier($id)
    {
        return $id;
    }

    protected function handleScalar(ProxyQueryInterface $queryBuilder, $alias, $field, $data)
    {
        if (empty($data['value'])) {
            return;
        }

        $id = self::fixIdentifier($data['value']->getId());

        if (isset($data['type']) && EqualType::TYPE_IS_NOT_EQUAL === $data['type']) {
            $queryBuilder->field($field)->notEqual($id);
        } else {
            $queryBuilder->field($field)->equals($id);
        }

        $this->active = true;
    }

    protected function handleMultiple(ProxyQueryInterface $queryBuilder, $alias, $field, $data)
    {
        if (0 === \count($data['value'])) {
            return;
        }

        $ids = [];
        foreach ($data['value'] as $value) {
            $ids[] = self::fixIdentifier($value->getId());
        }

        if (isset($data['type']) && EqualType::TYPE_IS_NOT_EQUAL === $data['type']) {
            $queryBuilder->field($field)->notIn($ids);
        } else {
            $queryBuilder->field($field)->in($ids);
        }

        $this->active = true;
    }
}