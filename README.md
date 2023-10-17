# Rivus

An application for receiving taking incoming Feeds (Atom, JSON, RSS, Social) into your own stream.
An application for publishing outgoing Social on your own domain using ActivityPub.

Using: [PHP], [libsodium](https://doc.libsodium.org/) and [ActivityPub](https://www.w3.org/TR/activitypub/)


## Subscribing

The first use-case for Rivus, when I was just building internally was to consume streams.
Most of my time is spent lurking on various sites: Facebook, LinkedIn, Reddit, Twitter, etc.
I like to be aware of whats happening and it's a great way to get all the headlines -- and (had historically) been trival to click-into any of them for more detail/discussion.

Normal/standard [Atom](https://validator.w3.org/feed/docs/atom.html), [RSS](https://validator.w3.org/feed/docs/rss2.html) or [JSONFeed](https://www.jsonfeed.org/) streams are simple to add: just add the link to your subscription.

Authenticated services are slightly more complicated.
A feature of running your own instance of Rivus is that you are in control of the stream.
A consequence of this is that you must use your own credintials when consuming those streams.

See the README for [Facebook](./doc/README-Facebook.md), [LinkedIn](./doc/README-LinkedIn.md) and [Reddit](./doc/README-Reddit.md)

## Publishing

For the case of publishing, Rivus believes in control for the author.
So, you'd simply create a post in Rivus of the proper type.
Your Rivus instance will publish the post, and generate Atom, RSS and JSONFeed formatted documents.
Rivus will also publish via ActivityPub which is a distributed/federated social-media protocol.


## Technology Stack

Rivus is designed to be very low-maintenance and we've tried to keep the dependency count very low.

### PHP

Rivus should run just fine on PHP 7 or 8.

### libsodium

For some of the encryption work we're depending on libsodium.
It's bundeled in with most PHP distributsions.

* https://libsodium.org/
* https://www.php.net/manual/en/book.sodium.php


### ActivityPub

* https://www.w3.org/TR/activitypub/
* https://www.w3.org/TR/activitystreams-vocabulary/
* https://socialhub.activitypub.rocks/t/guide-for-new-activitypub-implementers/479
* https://github.com/landrok/activitypub
* https://github.com/pterotype-project/activitypub-php

Follow Someone:

```
curl https://mastodon.social/@edoceo # and look for <link> or <script> for activity pub.
curl --header 'Accept: application/json' 'https://mastodon.social/@openthc'  | jq
curl --header 'Accept: application/json' https://mastodon.social/users/edoceo
```


## Creating a Site

To get the Rivus code to host a site you'll need to create the directory in `./var`.
We intentionally don't make the path automatically because then the system get's littered with new paths from all kinds of drive-by HTTP noise.
Better to serve them a `501` or `404` type resonse.

## Posting Publicly

Just write something.


## Sending Anon/Encrypted Message to Recipient

Encrypt a message to the Public Key; but not from any specific Source Key

```php
// Source
$message_crypt = sodium_crypto_box_seal($message_plain, $target_public_key);
// Target
$message_plain = sodium_crypto_box_seal_open($message_crypt, $target_key_pair);
```

## Sending Verified Encrypted Message to Recipient

```php
// Source
$crypt = rivus_box($plain, $source_sk, $target_pk)
// Target
$plain = rivus_box_open($crypt, $target_sk)
```
