<?php

namespace Drupal\mongo_to_entity\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Plugin\MigrationInterface;

/**
 * Provides a 'MongoToEntity' migrate source.
 *
 * @MigrateSource(
 *  id = "mongo_to_entity",
 *  source_module = "mongo_to_entity"
 * )
 */
class MongoToEntity extends SourcePluginBase {

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      '_id' => $this->t('ID'),
      'city' => $this->t('City'),
      'loc' => $this->t('Location'),
      'pop' => $this->t('Population'),
      'state' => $this->t('State'),
    ];
  }

  /**
   * Data obtained from the source plugin configuration.
   *
   * @var array[]
   *   Array of data rows, each one an array of values keyed by field names.
   */
  protected $dataRows = [];

  /**
   * Description of the unique ID fields for this source.
   *
   * @var array[]
   *   Each array member is keyed by a field name, with a value that is an
   *   array with a single member with key 'type' and value a column type such
   *   as 'integer'.
   */
  protected $ids = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $db_string = 'mongodb://10.0.2.2:27017/Cities';
    $mongo = new \MongoDB\Driver\Manager($db_string);

    $filter = [];
    $options = [];

    // Query mongo database
    $query = new \MongoDB\Driver\Query($filter, $options);
    $cursor = $mongo->executeQuery('Cities.cities', $query);
    $cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);
    $this->dataRows = $cursor->toArray();
    $this->ids = $configuration['ids'];
//    $this->ids = [
//      '_id' => [
//        'type' => 'string',
//      ],
//    ];
  }

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    return new \ArrayIterator($this->dataRows);
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return 'Mongo DB to Entity';
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return $this->ids;
  }

  /**
   * {@inheritdoc}
   */
  public function count($refresh = FALSE) {
    return count($this->dataRows);
  }

}
