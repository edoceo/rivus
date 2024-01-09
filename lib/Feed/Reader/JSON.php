<?php
/**
 * JSON Feed Reader
 *
 * SPDX-License-Identifier: MIT
 */

namespace Edoceo\Rivus\Feed\Reader;

class JSON extends \Edoceo\Rivus\Feed\Reader
{
	protected $data;

	protected $mime;

	protected $type;

	protected $url;

	function __construct(string $source)
	{
		$this->data = $source;
		$this->parse();
	}

	/**
	 * function _feed_from_json($data)
	 */
	function parse()
	{
		$data = json_decode($this->data, true);
		if (empty($data)) {
			throw new \Exception('Invalid JSONFeed Data');
		}

		return false;

	}

	function getItems()
	{
		$data = json_decode($this->data, true);
		if (empty($data)) {
			throw new \Exception('Invalid JSONFeed Data');
		}

		// Detect Reddit Type
		if (('Listing' == $data['kind']) && is_array($data['data']['children'])) {
			return $this->getItems_Reddit($data);
		}

		$ret = [];

		// Spin Each Channel
		// foreach ($this->xml->entry as $src) {

		// 	$rec = [];
		// 	$rec['id'] = md5(json_encode($src));
		// 	$rec['created_at'] = strtotime($src->published ?: $src->updated); // $src['created'];
		// 	$rec['expires_at'] = $rec['created_at'] + 86400;
		// 	// $rec['type'] = $url['host'];
		// 	$rec['name'] = trim($src->title);
		// 	$rec['link'] = $src->link['href']; // '] ?: $src['comments'];
		// 	$rec['source'] = json_encode($src);

		// 	// Would love if this supported ON CONFLICT
		// 	// $dbc->insert('post_incoming', $rec);

		// 	$ret[] = $rec;
		// }

		return $ret;
	}

	/**
	 *
	 */
	function getItems_Reddit($data)
	{
		$ret = [];
		$key_remove_list = $this->_reddit_key_remove_list();

		foreach ($data['data']['children'] as $src) {

			// echo json_encode($src, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			$src = $src['data'];
			foreach ($key_remove_list as $k) {
				unset($src[$k]);
			}
			ksort($src);

			$rec = [];
			$rec['id'] = sprintf('reddit-%s', $src['id']);
			$rec['created_at'] = $src['created'];
			$rec['expires_at'] = $src['created'] + 86400;
			$rec['type'] = 'reddit';
			$rec['name'] = $src['title'];
			$rec['source'] = json_encode($src);
			// $dbc->insert('post_incoming', $rec);

			$ret[] = $rec;
		}

		return $ret;
	}

	private function _reddit_key_remove_list()
	{
		$key_remove_list = [
			'all_awardings',
			'allow_live_comments',
			'approved_at_utc',
			'approved_by',
			'archived',
			// 'author': 'False_Gap_6619',
			'author_flair_background_color',
			'author_flair_css_class',
			'author_flair_richtext',
			'author_flair_template_id',
			'author_flair_text',
			'author_flair_text_color',
			'author_flair_type',
			// 'author_fullname': 't2_7cfr5s0n',
			'author_is_blocked',
			'author_patreon_flair',
			'author_premium',
			'awarders',
			'banned_at_utc',
			'banned_by',
			'can_gild',
			'can_mod_post',
			// 'category',
			'clicked',
			'content_categories',
			'contest_mode',
			// 'created': 1691217487,
			// 'created_utc': 1691217487,
			'discussion_type',
			'distinguished',
			// 'domain': 'v.redd.it',
			'downs',
			'edited',
			'gilded',
			'gildings',
			'hidden',
			'hide_score',
			// 'id',
			'is_created_from_ads_ui',
			'is_crosspostable',
			'is_meta',
			'is_original_content',
			'is_reddit_media_domain',
			'is_robot_indexable',
			// 'is_self',
			// 'is_video': true,
			'likes',
			'link_flair_background_color',
			'link_flair_css_class',
			'link_flair_richtext',
			'link_flair_text',
			'link_flair_text_color',
			'link_flair_type',
			'locked',
			// 'media',
			// 'media_embed',
			'media_only',
			'mod_note',
			'mod_reason_by',
			'mod_reason_title',
			// 'mod_reports',
			// 'name',
			'no_follow',
			// 'num_comments',
			// 'num_crossposts',
			// 'num_reports',
			'over_18',
			'parent_whitelist_status',
			// 'permalink': "/r/Damnthatsinteresting/comments/15io76c/amazing_footage_of_earth_during_a_spacewalk_on_iss/",
			'pinned',
			'post_hint',
			// 'preview',
			'pwls',
			'quarantine',
			'removal_reason',
			'removed_by',
			'removed_by_category',
			'report_reasons',
			// 'score',
			// "secure_media": {
			// 	"reddit_video": {
			// 		"bitrate_kbps": 2400,
			// 		"fallback_url": "https://v.redd.it/rwrxqdjpl8gb1/DASH_720.mp4?source=fallback",
			// 		"height": 720,
			// 		"width": 1280,
			// 		"scrubber_media_url": "https://v.redd.it/rwrxqdjpl8gb1/DASH_96.mp4",
			// 		"dash_url": "https://v.redd.it/rwrxqdjpl8gb1/DASHPlaylist.mpd?a=1693832773%2CYmM2Y2U5ODdhZTg5NjQ3NDM4NWFhMTlkOWI4MjQzMGQyOTdiNmFlMTA2YjBhYzcyNjMxMDkzMzBkYjM1OGQxNw%3D%3D&amp;v=1&amp;f=sd",
			// 		"duration": 90,
			// 		"hls_url": "https://v.redd.it/rwrxqdjpl8gb1/HLSPlaylist.m3u8?a=1693832773%2CMzZlNmE1ZTc3NDQyYjZjY2IwNjM0Y2ExMDMyZDk1OWMyYjA2ZDRhYzRjYmQ5ODI5OTg1MTk2MmYwNzBiNmMxYw%3D%3D&amp;v=1&amp;f=sd",
			// 		"is_gif",
			// 		"transcoding_status": "completed"
			// 	}
			// },
			// 'secure_media_embed',
			// 'selftext',
			// 'selftext_html',
			'send_replies',
			'spoiler',
			'stickied',
			// 'subreddit',
			// 'subreddit_id': 't5_2xxyj',
			// 'subreddit_name_prefixed': 'r/Damnthatsinteresting',
			'subreddit_subscribers',
			'subreddit_type',
			'suggested_sort',
			// 'thumbnail',
			// 'thumbnail_height',
			// 'thumbnail_width',
			// 'title',
			'top_awarded_type',
			// 'total_awards_received',
			'treatment_tags',
			// 'ups',
			// 'upvote_ratio',
			// 'url',
			// 'url_overridden_by_dest',
			'user_reports',
			'view_count',
			'visited',
			'whitelist_status',
			'wls',
		];

		return $key_remove_list;

	}


}
