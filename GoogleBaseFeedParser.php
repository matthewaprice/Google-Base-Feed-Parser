<?php
/*
USAGE:
# Input the feed in the constructor
$feed = new GoogleBaseFeedParser( 'http://domain.com/googlebasefeed.xml' );

# Set up the fields to retrieve
# here is a default base field "<entry>"
<entry>
	<title><![CDATA[Title Here]]></title>
	<link><![CDATA[http://www.domain.com/product/]]></link>
	<description><![CDATA[Description here]]></description>
	<g:expiration_date><![CDATA[2011-12-28]]></g:expiration_date>
	<g:id><![CDATA[ID VALUE HERE]]></g:id>
	<g:condition><![CDATA[new]]></g:condition>
	<g:product_type><![CDATA[Product Type Here]]></g:product_type>
	<g:brand><![CDATA[Nike]]></g:brand>
	<g:image_link><![CDATA[http://domain.com/image.jpg]]></g:image_link>
	<g:model_number><![CDATA[XXXXXX]]></g:model_number>
	<g:shipping_weight><![CDATA[0.00 kg]]></g:shipping_weight>
	<g:height><![CDATA[10.0000 cm]]></g:height>
	<g:length><![CDATA[5.0000 cm]]></g:length>
	<g:width><![CDATA[8.0000 cm]]></g:width>
	<g:google_product_category><![CDATA[ &gt; Baby &amp; Toddler]]></g:google_product_category>
	<g:gtin><![CDATA[4016977110396]]></g:gtin>
	<g:mpn><![CDATA[11039]]></g:mpn>
	<g:gender><![CDATA[unisex]]></g:gender>
	<g:age_group><![CDATA[kids]]></g:age_group>
	<g:availability><![CDATA[in stock]]></g:availability>
	<g:price><![CDATA[12.00 USD]]></g:price>
</entry>
if you need a field with the "g:" namespace, just place it without.  the class handles this
$fields = array( 'title','link','description','id','brand','image_link','model_number','google_product_category','price' );
$feed->setFieldsToRetrieve( $fields );		

# this gives you the results as an array to loop through
# uses the order of the fields you set in setFieldsToRetrieve()
$results = $feed->getFeedResults();		
example:
foreach ( $results as $result ) {
	echo $result['title'];
}

*/

class GoogleBaseFeedParser {

	/**** Private Variables ****/	
	private $xml_feed;

	/**** BEGIN Public Methods ****/
		
	/*
	 * __construct( $xml_feed ) 
	 * @var $xml_feed - Google Base Feed URL
	 */
	public function __construct( $xml_feed ) {
	
		$this->xml_feed = $xml_feed;
		
	}

	/*
	 * setFieldsToRetrieve()
	 * the fields are named the same as their names in one "<entry>" in the feed
	 */
	public function setFieldsToRetrieve( $fields ) {
	
		$this->fields = $fields;
	
	}
	
	/*
	 * getFeedResults()
	 * uses private function loadFeed();
	 * takes the feed and iterates through the <entry>s
	 * builds an array of just the pieces necessary 
	 */
	public function getFeedResults() {
		
		$feed_results = $this->loadFeed();
		$i = 0;
		foreach ( $feed_results->entry as $entry ) {
			// get namespace references ( "g:" ) in this case
			$g = $entry->children("http://base.google.com/ns/1.0");			
			
			// build array containing only data that we need to create a product
			foreach ( $this->fields as $field ) {
			  	
			  	// we need to check to see if the field being requested has the "g:" namespace.
			  	// if the field does not exist as a regulr xml field, then try to load it from the "g:" namespace
			  	if ( $entry->{$field} == '' ) {
			  		$results[$i][$field] = (string)$g->{$field};
			  	} else {
				  	$results[$i][$field] = (string)$entry->{$field};
				}
			  
			}
		  	
		$i++;	
		}
		
		return $results;
				
	}

	/**** END Public Methods ****/

	/**** BEGIN Private Methods ****/
		
	/*
	 * Takes the xml file and loads it for use
	 */	
	private function loadFeed() {
		
		$feed = simplexml_load_file( $this->xml_feed );
		return $feed;
		
	}

	/**** END Private Methods ****/
	
}
?>