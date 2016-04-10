<?php

namespace Nab3aBundle\Tests\Logger;

use Evenement\EventEmitter;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Nab3aBundle\Logger\LogMessagePlugin;

class LogMessagePluginTest extends \PHPUnit_Framework_TestCase
{
    public function testLog()
    {
        $logger = new Logger('test');
        $handler = new TestHandler();
        $logger->pushHandler($handler);

        $plugin = new LogMessagePlugin();
        $plugin->setLogger($logger);

        $emitter = new EventEmitter();
        $plugin->attachEvents($emitter);

        $emitter->emit('keep-alive', ["\r\n"]);
        $emitter->emit('delete', ['{"delete":{"status":{"id":1234,"id_str":"1234","user_id":3,"user_id_str":"3"}}}']);
//        $emitter->emit('scrub_geo', []);
        $emitter->emit('limit', ['{"limit":{"track":1234}}']);
        $emitter->emit('status_withheld', ['{"status_withheld":{"id":1234567890,"user_id":123456,"withheld_in_countries":["DE","AR"]}}']);
        $emitter->emit('user_withheld', ['{"user_withheld":{"id":123456,"withheld_in_countries":["DE","AR"]}}']);
        $emitter->emit('disconnect', ['{"disconnect":{"code":4,"stream_name":"","reason":""}}']);
        $emitter->emit('warning', ['{"warning":{"code":"FALLING_BEHIND","message":"Your connection is falling behind and messages are being queued for delivery to you. Your queue is now over 60% full. You will be disconnected when the queue is full.","percent_full":60}}']);
        $emitter->emit('tweet', ['{"created_at":"Wed Apr 06 12:54:10 +0000 2016","id":717696904906350600,"id_str":"717696904906350592","text":"Improved image sizes coming soon to the API. Details here on the dev forums, https://t.co/ivGr2Y2Pbx and on the blog https://t.co/dduQ4dVwa7","entities":{"hashtags":[],"symbols":[],"user_mentions":[],"urls":[{"url":"https://t.co/ivGr2Y2Pbx","expanded_url":"https://twittercommunity.com/t/coming-soon-improved-image-sizes-to-the-api/64601","display_url":"twittercommunity.com/t/coming-soon-…","indices":[77,100]}],"media":[{"id":717696555801821200,"id_str":"717696555801821185","indices":[117,140],"media_url":"http://pbs.twimg.com/media/CfXFNwkWwAETFB2.jpg","media_url_https":"https://pbs.twimg.com/media/CfXFNwkWwAETFB2.jpg","url":"https://t.co/dduQ4dVwa7","display_url":"pic.twitter.com/dduQ4dVwa7","expanded_url":"http://twitter.com/twitterapi/status/717696904906350592/photo/1","type":"photo","sizes":{"small":{"w":340,"h":227,"resize":"fit"},"medium":{"w":600,"h":400,"resize":"fit"},"thumb":{"w":150,"h":150,"resize":"crop"},"large":{"w":600,"h":400,"resize":"fit"}}}]},"extended_entities":{"media":[{"id":717696555801821200,"id_str":"717696555801821185","indices":[117,140],"media_url":"http://pbs.twimg.com/media/CfXFNwkWwAETFB2.jpg","media_url_https":"https://pbs.twimg.com/media/CfXFNwkWwAETFB2.jpg","url":"https://t.co/dduQ4dVwa7","display_url":"pic.twitter.com/dduQ4dVwa7","expanded_url":"http://twitter.com/twitterapi/status/717696904906350592/photo/1","type":"photo","sizes":{"small":{"w":340,"h":227,"resize":"fit"},"medium":{"w":600,"h":400,"resize":"fit"},"thumb":{"w":150,"h":150,"resize":"crop"},"large":{"w":600,"h":400,"resize":"fit"}}}]},"truncated":false,"source":"<a href=\"https://about.twitter.com/products/tweetdeck\" rel=\"nofollow\">TweetDeck</a>","in_reply_to_status_id":null,"in_reply_to_status_id_str":null,"in_reply_to_user_id":null,"in_reply_to_user_id_str":null,"in_reply_to_screen_name":null,"user":{"id":6253282,"id_str":"6253282","name":"Twitter API","screen_name":"twitterapi","location":"San Francisco, CA","description":"The Real Twitter API. I tweet about API changes, service issues and happily answer questions about Twitter and our API. Don\'t get an answer? It\'s on my website.","url":"http://t.co/78pYTvWfJd","entities":{"url":{"urls":[{"url":"http://t.co/78pYTvWfJd","expanded_url":"http://dev.twitter.com","display_url":"dev.twitter.com","indices":[0,22]}]},"description":{"urls":[]}},"protected":false,"followers_count":5832855,"friends_count":47,"listed_count":13047,"created_at":"Wed May 23 06:01:13 +0000 2007","favourites_count":27,"utc_offset":-25200,"time_zone":"Pacific Time (US & Canada)","geo_enabled":true,"verified":true,"statuses_count":3563,"lang":"en","contributors_enabled":false,"is_translator":false,"is_translation_enabled":false,"profile_background_color":"C0DEED","profile_background_image_url":"http://pbs.twimg.com/profile_background_images/656927849/miyt9dpjz77sc0w3d4vj.png","profile_background_image_url_https":"https://pbs.twimg.com/profile_background_images/656927849/miyt9dpjz77sc0w3d4vj.png","profile_background_tile":true,"profile_image_url":"http://pbs.twimg.com/profile_images/2284174872/7df3h38zabcvjylnyfe3_normal.png","profile_image_url_https":"https://pbs.twimg.com/profile_images/2284174872/7df3h38zabcvjylnyfe3_normal.png","profile_banner_url":"https://pbs.twimg.com/profile_banners/6253282/1431474710","profile_link_color":"0084B4","profile_sidebar_border_color":"C0DEED","profile_sidebar_fill_color":"DDEEF6","profile_text_color":"333333","profile_use_background_image":true,"has_extended_profile":false,"default_profile":false,"default_profile_image":false,"following":false,"follow_request_sent":false,"notifications":false},"geo":null,"coordinates":null,"place":null,"contributors":null,"is_quote_status":false,"retweet_count":97,"favorite_count":122,"favorited":false,"retweeted":false,"possibly_sensitive":false,"possibly_sensitive_appealable":false,"lang":"en"}']);
        $records = $handler->getRecords();

        $record = array_shift($records);
        $this->assertEquals($record['message'], 'keep alive');
    }
}
