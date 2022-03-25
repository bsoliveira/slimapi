<?php

declare(strict_types=1);

namespace App\Factory;

use PDO;
use Valitron\Validator;
use Psr\Container\ContainerInterface;

class ValitronFactory
{
    /**
     * Factory
     *
     * @param ContainerInterface $container
     * @return Validator
     */
    public static function create(ContainerInterface $container): Validator
    {
        $db = $container->get(PDO::class);

        /**
         *  Checks if this field value exists in the database. Optionally set a ignoreId and value to ignore, useful when updating records to ignore itself.
         *
         *  ['unique', 'tablename', 'field', ignoreId]
         *
         *  Exemple:
         *
         *  'email' => [
         *      ['unique', 'users', 'email', $id]
         *   ],
         */
        Validator::addRule('unique', function ($field, $value, array $params, array $fields) use ($db) {
            $tablename = $params[0];
            $field = $params[1];
            $ignoreId = $params[2] ?? 0;

            $stmt = $db->prepare("select count(*) as count from `{$tablename}` where `{$field}` = :value and `id` != {$ignoreId}");
            $stmt->bindValue(':value', $value);
            $stmt->execute();

            $count = (int) $stmt->fetchColumn();

            return $count === 0;
        }, "{field} is already in use.");

        /**
         *  Returns TRUE for "1", "true", "on" and "yes". Returns FALSE otherwise.
         */
        Validator::addRule('booleanic', function ($field, $value, array $params, array $fields) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
        }, "The {field} must be a boolean.");

        /**
         *  Fails if field contains anything other than alphabetic characters or spaces.
         */
        Validator::addRule('alphaSpace', function ($field, $value, array $params, array $fields) {
            if ($value === null) {
                return true;
            }
            return (bool) preg_match('/^[A-Z ]+$/i', $value);
        }, "The {field} field may only contain alphabetical characters and spaces.");

        /**
         *  Fails if field contains anything other than alphanumeric or space characters.
         */
        Validator::addRule('alphaNumSpace', function ($field, $value, array $params, array $fields) {
            return (bool) preg_match('/^[A-Z0-9 ]+$/i', $value);
        }, "The {field} field may only contain alphanumeric and space characters.");

        $v = new Validator();

        // Disable prepending the labels
        $v->setPrependLabels(false);

        return $v;
    }
}
