<?php

namespace Openpp\NotificationHubsRest\Notification\Tests;

use Openpp\NotificationHubsRest\Notification\TemplateNotification;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class TemplateNotificationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetContentType()
    {
        $notification = new TemplateNotification(array('message' => 'Hello!'));

        $this->assertEquals('application/json;charset=utf-8', $notification->getContentType());
    }

    public function testGetHeadersWithNoTags()
    {
        $notification = new TemplateNotification(array('message' => 'Hello!'));

        $this->assertEquals(array(
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: template',
        ), $notification->getHeaders());
    }

    public function testGetHeadersWithATag()
    {
        $notification = new TemplateNotification(array('message' => 'Hello!'), array(), 'female');

        $this->assertEquals(array(
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: template',
            'ServiceBusNotification-Tags: female'
        ), $notification->getHeaders());
    }

    public function testGetHeadersWithTags()
    {
        $notification = new TemplateNotification(array('message' => 'Hello!'), array(), array('ios', 'female', 'japanese'));

        $this->assertEquals(array(
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: template',
            'ServiceBusNotification-Tags: ios || female || japanese'
        ), $notification->getHeaders());
    }

    public function testGetHeadersWithTagExpression()
    {
        $notification = new TemplateNotification(array('message' => 'Hello!'), array(), '(ios && female) || japanese');

        $this->assertEquals(array(
            'Content-Type: application/json;charset=utf-8',
            'ServiceBusNotification-Format: template',
            'ServiceBusNotification-Tags: (ios && female) || japanese'
        ), $notification->getHeaders());
    }

    public function testBuildUri()
    {
        $notification = new TemplateNotification(array('message' => 'Hello!'));

        $this->assertEquals(
                'aaa.servicebus.windows.net/myhub/messages/',
                $notification->buildUri('aaa.servicebus.windows.net/', 'myhub'));
    }

    public function testScrapeResponse()
    {
        $notification = new TemplateNotification(array('message' => 'Hello!'));
        $notification->scrapeResponse('');
    }

    public function testGetPayloadWithArray()
    {
        $notification = new TemplateNotification(array('message' => 'Hello!', 'name' => 'John'));
        $payload = $notification->getPayload();

        $this->assertJsonStringEqualsJsonString('{"message" : "Hello!", "name" : "John" }', $payload);
    }

    public function testGetPayloadWithScalar()
    {
        $notification = new TemplateNotification('{"message" : "Hello!", "name" : "John" }');
        $payload = $notification->getPayload();

        $this->assertJsonStringEqualsJsonString('{"message" : "Hello!", "name" : "John" }', $payload);
    }
}