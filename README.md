# UrlSpool
## A simple script that takes a pattern and some data, then generates random url(s)
Originally created to generate urls to pipe into ab or some other throughput measuring tool

<pre>
	USAGE:
php UrlSpool.php pattern datalists [count]
	pattern   : see readme for syntax
	datalists : a valid json encoded array of arrays
	count     : optional, defaults to 1, number of line-delimited urls to output
</pre>

The syntax is limited to var, if var=, and if var !=, which has been adequate

## Pattern syntax:
	* $# for a random element list #
	* <$X=VALUE:$#> for a random element of $# if $X = VALUE, where $X is some list that resolved earlier in the pattern
	* <$X!=VALUE:$#> for a random element of $# if $X != VALUE, where $X is some list that resolved earlier in the pattern

## Notes:
	* No $ or < or > or ~ characters allowed in url except for in rules
	* Nested conditionals are fine
	* Patterns resolve left-to-right
	* Key dependencies must be orderd USE first, then CONDITIONAL - that is, asking if $2 = 'mek' is not allowed before $2 has been resolved
	* List of lists is 0 indexed (the first list is $0)

## Example:
<pre>
php UrlSpool.php 'http://example.com/$0&lt;$0!=flush~/$1&lt;$0=set~/$2>>' '[["get", "set", "flush"], ["mek", "wek", "bek"], [1, 2, 3]]' 10
</pre>

could produce:
<pre>
http://example.com/get/bek
http://example.com/get/wek
http://example.com/flush
http://example.com/get/mek
http://example.com/flush
http://example.com/get/mek
http://example.com/flush
http://example.com/set/mek/1
http://example.com/flush
http://example.com/flush
</pre>
