<?php

use Carbon\Carbon;

class Vbe_Upcoming_Events extends \WP_REST_Controller
{
    public function __construct() {
        $this->namespace = 'vb/v1';
        $this->rest_base = 'events';
    }

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/upcoming',
            [
                [
                    'methods'               => WP_REST_Server::READABLE,
                    'callback'              => [$this, 'get_upcoming_items'],
                    'permission_callback'   => [$this, 'get_upcoming_items_permission_check'],
                    'args'                  => $this->get_collection_params()
                ],
                'schema'    => [$this, 'get_upcoming_items_schema']
            ]
        );
    }

    /**
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true Everything is readable.
     */
    public function get_upcoming_items_permission_check($request) {
        return true;
    }

    /**
     * Retrieves all upcoming events (for the coming month)
     *
     * @param WP_REST_Request $request
     * @return WP_Error|WP_REST_Response
     */
    public function get_upcoming_items($request) {
        $response = [
            'type'  => $request->get_param('type'),
        ];

        if ($response['type'] === 'json') {
            return $this->prepare_items_json_response($request);
        }

        return $this->prepare_items_xml_response($request);
    }

    /**
     * Retrieves the post type's schema, conforming to JSON Schema.
     *
     * @since 4.7.0
     *
     * @return array Item schema data.
     */
    public function get_item_schema() {
        $schema = [
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'type',
            'type'       => 'object',
            'properties' => []
        ];

        return $this->add_additional_fields_schema( $schema );
    }

    public function get_collection_params() {
        return [
            'type'  => $this->get_context_param(
                [
                    'description'   => 'The type of response required, either an XML file or a JSON response.',
                    'default'       => 'xml'
                ]
            )
        ];
    }

    /**
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     * @throws Exception
     */
    protected function prepare_items_json_response($request) {
        $data = []; # This holds the data for the events.
        $events = $this->get_upcoming_events_array();

        foreach ($events as $event) {
            $data [$event->ID] = [
                'title'     => $event->post_title,
                'content'   => get_the_excerpt($event->ID),
                'media'     => [
                    'html'      => get_the_post_thumbnail($event->ID, 'medium'),
                    'url'       => get_the_post_thumbnail_url($event->ID),
                ],
                'published' => $event->post_date,
            ];

            if (function_exists('get_field')) {
                try {
                    $date = new Carbon((string) get_field('start_datum', $event->ID));
                } catch (Exception $e) {
                    new WP_Error( $e->getCode(), $e->getMessage() );
                }

                $data [$event->ID] ['start_date'] = $date->isoFormat('DD-MM-YYYY');
                $data [$event->ID] ['auteur'] = $date->isoFormat('DD-MM-YYYY');
            }
        }

        return rest_ensure_response($data);
    }



    /**
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    protected function prepare_items_xml_response($request) {
        $pubDate = Carbon::now()->day(20)->toRssString();
        $events = $this->get_upcoming_events_array();

        header('Content-Type: application/xml');
        print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<rss version=\"2.0\"
        xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"
        xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\"
        xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
        xmlns:atom=\"http://www.w3.org/2005/Atom\"
        xmlns:sy=\"http://purl.org/rss/1.0/modules/syndication/\"
        xmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\"
        xmlns:media=\"http://search.yahoo.com/mrss/\"
        >\r\n";
        print "<channel>\r\n";
        print "<language>nl</language>\r\n";
        print "<title>Aankomende evenementen</title>\r\n";
        print "<sy:updatePeriod>monthly</sy:updatePeriod>\r\n";
        print "<sy:updateFrequency>1</sy:updateFrequency>\r\n";
        print "<lastBuildDate>$pubDate</lastBuildDate>\r\n";
        foreach($events as $event) {
            try {
                $date = new Carbon((string) get_field('start_datum', $event->ID));
            } catch (Exception $e) {
                return new WP_Error( $e->getCode(), $e->getMessage() );
            }

            $date = $date->isoFormat('DD-MM-YYYY');

            include __DIR__ . '/../partials/vb-e-public-xml-event.php';
        }
        print "</channel>\r\n";
        print "</rss>\r\n";
        die;
    }

    /**
     * @return array|WP_Post[]
     */
    protected function get_upcoming_events_array(): array {
        $args = [
            'post_type'     => 'evenement',
            'posts_per_page'=> -1,
            'meta_query'    => [
                'relation'      => 'AND',
                [
                    'key'       => 'start_datum',
                    'value'     => (int) self::lowerLimit(),
                    'compare'   => '>=',
                    'type'      => 'NUMERIC'
                ],
                [
                    'key'       => 'start_datum',
                    'value'     => (int) self::upperLimit(),
                    'compare'   => '<',
                    'type'      => 'NUMERIC'
                ]
            ],
            'orderby'           => 'meta_value_num',
            'meta_key'          => 'start_datum',
            'order'             => 'ASC'
        ];


        $q = new WP_Query($args);

        if (!$q->have_posts()) {
            return [];
        }

        return $q->posts;
    }

    private static function lowerLimit(): string
    {
        $date = Carbon::now()->addMonths(1)->firstOfMonth();
        return $date->isoFormat('YYYYMMDD');
    }

    private static function upperLimit(): string
    {
        $date = Carbon::now()->addMonths(2)->firstOfMonth();
        return $date->isoFormat('YYYYMMDD');
    }
}
