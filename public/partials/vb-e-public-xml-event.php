<?php
/**
 * @var WP_Post $event
 * @var $date
 * @var $pubDate
 */

?>
<item>
    <ID><?=$event->ID?></ID>
    <title><?=$event->post_title?></title>
    <description><![CDATA[<?=get_the_excerpt($event->ID)?>]]></description>
    <media:content url="<?=get_the_post_thumbnail_url($event->ID, 'medium')?>" medium="image" />
    <pubDate><?=$pubDate?></pubDate>
    <dc:creator><?=$date?></dc:creator>
    <link><?=get_the_permalink($event->ID)?></link>
</item>
