<?php

/**
 * @file
 * Contains \Drupal\rsvplist\Controller\ReportController.
 */

namespace Drupal\rsvplist\Controller;

use Drupal\Core\Database\Connection;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for RSVP List Report
 */

class ReportController extends ControllerBase
{

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('database')
        );
    }

    /**
     * Gets all RSVPs for all nodes.
     * 
     * @return array
     */
    protected function load()
    {
        $select = $this->connection->select('rsvplist', 'r');
        // This joins the users table to get the entry creator's username.
        $select->join('users_field_data', 'u', 'r.uid = u.uid');
        // This joins the node table to get the event's name.
        $select->join('node_field_data', 'n', 'r.nid = n.nid');

        // This selects specific fields for the output.
        $select->addField('u', 'name', 'username');
        $select->addField('n', 'title');
        $select->addField('r', 'mail');
        $entries = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);
        return $entries;
    }

    /**
     * Creates the report page.
     * 
     * @return array
     * Render array for report output.
     */
    public function report()
    {
        $content = array();
        $content['message'] = array(
            '#markup' => $this->t('Below is a list of all event RSVPs
            including username, email address and the name of the
            event they will be attending.'),
        );
        $headers = array(
            t('Name'),
            t('Event'),
            t('Email')
        );
        $rows = array();
        foreach  ($entries = $this->load() as $entry)
        {
            // Sanitize each entry.
            $rows[] = array_map('\Drupal\Component\Utility\Html::escape', $entry);
        }
        $content['table'] = array(
            '#type' => 'table',
            '#header' => $headers,
            '#rows' => $rows,
            '#empty' => t('No entries available.'),
        );
        // Don't use cache for this page.
        $content['#cache']['max-age'] = 0;
        return $content;
    }
}
