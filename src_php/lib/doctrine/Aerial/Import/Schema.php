<?php
/**
 * Created by IntelliJ IDEA.
 * User: danny
 * Date: 2011/01/19
 * Time: 12:26 AM
 */
 
class Aerial_Import_Schema extends Doctrine_Import_Schema
{

    /**
     * importSchema
     *
     * A method to import a Schema and translate it into a Doctrine_Record object
     *
     * @param  string $schema       The file containing the XML schema
     * @param  string $format       Format of the schema file
     * @param  string $directory    The directory where the Doctrine_Record class will be written
     * @param  array  $models       Optional array of models to import
     *
     * @return void
     */
    public function importSchema($schema, $format = 'yml', $directory = null, $models = array())
    {
        $schema = (array) $schema;
        $definitions = array();

        $array = $this->buildSchema($schema, $format);

        if (count($array) == 0) {
            throw new Doctrine_Import_Exception(
                sprintf('No ' . $format . ' schema found in ' . implode(", ", $schema))
            );
        }

        foreach ($array as $name => $definition) {
            if ( ! empty($models) && !in_array($definition['className'], $models)) {
                continue;
            }

            $definitions[] = $definition;
        }

        return $definitions;
    }
}
