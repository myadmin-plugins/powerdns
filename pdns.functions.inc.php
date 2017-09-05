<?php
/**
 * Power DNS Related Functionality
 * @author Joe Huss <detain@interserver.net>
 * @copyright 2017
 * @package MyAdmin
 * @category DNS
 */

use \MyDb\Mdb2\Db as db_mdb2;

require_once __DIR__.'/../../vendor/poweradmin/poweradmin/inc/error.inc.php';

global $rtypes, $server_types, $valid_tlds;

// Version 2017072000, Last Updated Thu Jul 20 07:07:01 2017 UTC
// 1547 TLDs
// http://data.iana.org/TLD/tlds-alpha-by-domain.txt
$valid_tlds = ['aaa', 'aarp', 'abarth', 'abb', 'abbott', 'abbvie', 'abc', 'able', 'abogado', 'abudhabi', 'ac', 'academy', 'accenture', 'accountant', 'accountants', 'aco', 'active', 'actor', 'ad', 'adac', 'ads', 'adult', 'ae', 'aeg', 'aero', 'aetna', 'af', 'afamilycompany', 'afl', 'africa', 'ag', 'agakhan', 'agency', 'ai', 'aig', 'aigo', 'airbus', 'airforce', 'airtel', 'akdn', 'al', 'alfaromeo', 'alibaba', 'alipay', 'allfinanz', 'allstate', 'ally', 'alsace', 'alstom', 'am', 'americanexpress', 'americanfamily', 'amex', 'amfam', 'amica', 'amsterdam', 'analytics', 'android', 'anquan', 'anz', 'ao', 'aol', 'apartments', 'app', 'apple', 'aq', 'aquarelle', 'ar', 'arab', 'aramco', 'archi', 'army', 'arpa', 'art', 'arte', 'as', 'asda', 'asia', 'associates', 'at', 'athleta', 'attorney', 'au', 'auction', 'audi', 'audible', 'audio', 'auspost', 'author', 'auto', 'autos', 'avianca', 'aw', 'aws', 'ax', 'axa', 'az', 'azure', 'ba', 'baby', 'baidu', 'banamex', 'bananarepublic', 'band', 'bank', 'bar', 'barcelona', 'barclaycard', 'barclays', 'barefoot', 'bargains', 'baseball', 'basketball', 'bauhaus', 'bayern', 'bb', 'bbc', 'bbt', 'bbva', 'bcg', 'bcn', 'bd', 'be', 'beats', 'beauty', 'beer', 'bentley', 'berlin', 'best', 'bestbuy', 'bet', 'bf', 'bg', 'bh', 'bharti', 'bi', 'bible', 'bid', 'bike', 'bing', 'bingo', 'bio', 'biz', 'bj', 'black', 'blackfriday', 'blanco', 'blockbuster', 'blog', 'bloomberg', 'blue', 'bm', 'bms', 'bmw', 'bn', 'bnl', 'bnpparibas', 'bo', 'boats', 'boehringer', 'bofa', 'bom', 'bond', 'boo', 'book', 'booking', 'boots', 'bosch', 'bostik', 'boston', 'bot', 'boutique', 'box', 'br', 'bradesco', 'bridgestone', 'broadway', 'broker', 'brother', 'brussels', 'bs', 'bt', 'budapest', 'bugatti', 'build', 'builders', 'business', 'buy', 'buzz', 'bv', 'bw', 'by', 'bz', 'bzh', 'ca', 'cab', 'cafe', 'cal', 'call', 'calvinklein', 'cam', 'camera', 'camp', 'cancerresearch', 'canon', 'capetown', 'capital', 'capitalone', 'car', 'caravan', 'cards', 'care', 'career', 'careers', 'cars', 'cartier', 'casa', 'case', 'caseih', 'cash', 'casino', 'cat', 'catering', 'catholic', 'cba', 'cbn', 'cbre', 'cbs', 'cc', 'cd', 'ceb', 'center', 'ceo', 'cern', 'cf', 'cfa', 'cfd', 'cg', 'ch', 'chanel', 'channel', 'chase', 'chat', 'cheap', 'chintai', 'chloe', 'christmas', 'chrome', 'chrysler', 'church', 'ci', 'cipriani', 'circle', 'cisco', 'citadel', 'citi', 'citic', 'city', 'cityeats', 'ck', 'cl', 'claims', 'cleaning', 'click', 'clinic', 'clinique', 'clothing', 'cloud', 'club', 'clubmed', 'cm', 'cn', 'co', 'coach', 'codes', 'coffee', 'college', 'cologne', 'com', 'comcast', 'commbank', 'community', 'company', 'compare', 'computer', 'comsec', 'condos', 'construction', 'consulting', 'contact', 'contractors', 'cooking', 'cookingchannel', 'cool', 'coop', 'corsica', 'country', 'coupon', 'coupons', 'courses', 'cr', 'credit', 'creditcard', 'creditunion', 'cricket', 'crown', 'crs', 'cruise', 'cruises', 'csc', 'cu', 'cuisinella', 'cv', 'cw', 'cx', 'cy', 'cymru', 'cyou', 'cz', 'dabur', 'dad', 'dance', 'data', 'date', 'dating', 'datsun', 'day', 'dclk', 'dds', 'de', 'deal', 'dealer', 'deals', 'degree', 'delivery', 'dell', 'deloitte', 'delta', 'democrat', 'dental', 'dentist', 'desi', 'design', 'dev', 'dhl', 'diamonds', 'diet', 'digital', 'direct', 'directory', 'discount', 'discover', 'dish', 'diy', 'dj', 'dk', 'dm', 'dnp', 'do', 'docs', 'doctor', 'dodge', 'dog', 'doha', 'domains', 'dot', 'download', 'drive', 'dtv', 'dubai', 'duck', 'dunlop', 'duns', 'dupont', 'durban', 'dvag', 'dvr', 'dz', 'earth', 'eat', 'ec', 'eco', 'edeka', 'edu', 'education', 'ee', 'eg', 'email', 'emerck', 'energy', 'engineer', 'engineering', 'enterprises', 'epost', 'epson', 'equipment', 'er', 'ericsson', 'erni', 'es', 'esq', 'estate', 'esurance', 'et', 'etisalat', 'eu', 'eurovision', 'eus', 'events', 'everbank', 'exchange', 'expert', 'exposed', 'express', 'extraspace', 'fage', 'fail', 'fairwinds', 'faith', 'family', 'fan', 'fans', 'farm', 'farmers', 'fashion', 'fast', 'fedex', 'feedback', 'ferrari', 'ferrero', 'fi', 'fiat', 'fidelity', 'fido', 'film', 'final', 'finance', 'financial', 'fire', 'firestone', 'firmdale', 'fish', 'fishing', 'fit', 'fitness', 'fj', 'fk', 'flickr', 'flights', 'flir', 'florist', 'flowers', 'fly', 'fm', 'fo', 'foo', 'food', 'foodnetwork', 'football', 'ford', 'forex', 'forsale', 'forum', 'foundation', 'fox', 'fr', 'free', 'fresenius', 'frl', 'frogans', 'frontdoor', 'frontier', 'ftr', 'fujitsu', 'fujixerox', 'fun', 'fund', 'furniture', 'futbol', 'fyi', 'ga', 'gal', 'gallery', 'gallo', 'gallup', 'game', 'games', 'gap', 'garden', 'gb', 'gbiz', 'gd', 'gdn', 'ge', 'gea', 'gent', 'genting', 'george', 'gf', 'gg', 'ggee', 'gh', 'gi', 'gift', 'gifts', 'gives', 'giving', 'gl', 'glade', 'glass', 'gle', 'global', 'globo', 'gm', 'gmail', 'gmbh', 'gmo', 'gmx', 'gn', 'godaddy', 'gold', 'goldpoint', 'golf', 'goo', 'goodhands', 'goodyear', 'goog', 'google', 'gop', 'got', 'gov', 'gp', 'gq', 'gr', 'grainger', 'graphics', 'gratis', 'green', 'gripe', 'grocery', 'group', 'gs', 'gt', 'gu', 'guardian', 'gucci', 'guge', 'guide', 'guitars', 'guru', 'gw', 'gy', 'hair', 'hamburg', 'hangout', 'haus', 'hbo', 'hdfc', 'hdfcbank', 'health', 'healthcare', 'help', 'helsinki', 'here', 'hermes', 'hgtv', 'hiphop', 'hisamitsu', 'hitachi', 'hiv', 'hk', 'hkt', 'hm', 'hn', 'hockey', 'holdings', 'holiday', 'homedepot', 'homegoods', 'homes', 'homesense', 'honda', 'honeywell', 'horse', 'hospital', 'host', 'hosting', 'hot', 'hoteles', 'hotels', 'hotmail', 'house', 'how', 'hr', 'hsbc', 'ht', 'htc', 'hu', 'hughes', 'hyatt', 'hyundai', 'ibm', 'icbc', 'ice', 'icu', 'id', 'ie', 'ieee', 'ifm', 'ikano', 'il', 'im', 'imamat', 'imdb', 'immo', 'immobilien', 'in', 'industries', 'infiniti', 'info', 'ing', 'ink', 'institute', 'insurance', 'insure', 'int', 'intel', 'international', 'intuit', 'investments', 'io', 'ipiranga', 'iq', 'ir', 'irish', 'is', 'iselect', 'ismaili', 'ist', 'istanbul', 'it', 'itau', 'itv', 'iveco', 'iwc', 'jaguar', 'java', 'jcb', 'jcp', 'je', 'jeep', 'jetzt', 'jewelry', 'jio', 'jlc', 'jll', 'jm', 'jmp', 'jnj', 'jo', 'jobs', 'joburg', 'jot', 'joy', 'jp', 'jpmorgan', 'jprs', 'juegos', 'juniper', 'kaufen', 'kddi', 'ke', 'kerryhotels', 'kerrylogistics', 'kerryproperties', 'kfh', 'kg', 'kh', 'ki', 'kia', 'kim', 'kinder', 'kindle', 'kitchen', 'kiwi', 'km', 'kn', 'koeln', 'komatsu', 'kosher', 'kp', 'kpmg', 'kpn', 'kr', 'krd', 'kred', 'kuokgroup', 'kw', 'ky', 'kyoto', 'kz', 'la', 'lacaixa', 'ladbrokes', 'lamborghini', 'lamer', 'lancaster', 'lancia', 'lancome', 'land', 'landrover', 'lanxess', 'lasalle', 'lat', 'latino', 'latrobe', 'law', 'lawyer', 'lb', 'lc', 'lds', 'lease', 'leclerc', 'lefrak', 'legal', 'lego', 'lexus', 'lgbt', 'li', 'liaison', 'lidl', 'life', 'lifeinsurance', 'lifestyle', 'lighting', 'like', 'lilly', 'limited', 'limo', 'lincoln', 'linde', 'link', 'lipsy', 'live', 'living', 'lixil', 'lk', 'loan', 'loans', 'locker', 'locus', 'loft', 'lol', 'london', 'lotte', 'lotto', 'love', 'lpl', 'lplfinancial', 'lr', 'ls', 'lt', 'ltd', 'ltda', 'lu', 'lundbeck', 'lupin', 'luxe', 'luxury', 'lv', 'ly', 'ma', 'macys', 'madrid', 'maif', 'maison', 'makeup', 'man', 'management', 'mango', 'map', 'market', 'marketing', 'markets', 'marriott', 'marshalls', 'maserati', 'mattel', 'mba', 'mc', 'mcd', 'mcdonalds', 'mckinsey', 'md', 'me', 'med', 'media', 'meet', 'melbourne', 'meme', 'memorial', 'men', 'menu', 'meo', 'merckmsd', 'metlife', 'mg', 'mh', 'miami', 'microsoft', 'mil', 'mini', 'mint', 'mit', 'mitsubishi', 'mk', 'ml', 'mlb', 'mls', 'mm', 'mma', 'mn', 'mo', 'mobi', 'mobile', 'mobily', 'moda', 'moe', 'moi', 'mom', 'monash', 'money', 'monster', 'montblanc', 'mopar', 'mormon', 'mortgage', 'moscow', 'moto', 'motorcycles', 'mov', 'movie', 'movistar', 'mp', 'mq', 'mr', 'ms', 'msd', 'mt', 'mtn', 'mtr', 'mu', 'museum', 'mutual', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'nab', 'nadex', 'nagoya', 'name', 'nationwide', 'natura', 'navy', 'nba', 'nc', 'ne', 'nec', 'net', 'netbank', 'netflix', 'network', 'neustar', 'new', 'newholland', 'news', 'next', 'nextdirect', 'nexus', 'nf', 'nfl', 'ng', 'ngo', 'nhk', 'ni', 'nico', 'nike', 'nikon', 'ninja', 'nissan', 'nissay', 'nl', 'no', 'nokia', 'northwesternmutual', 'norton', 'now', 'nowruz', 'nowtv', 'np', 'nr', 'nra', 'nrw', 'ntt', 'nu', 'nyc', 'nz', 'obi', 'observer', 'off', 'office', 'okinawa', 'olayan', 'olayangroup', 'oldnavy', 'ollo', 'om', 'omega', 'one', 'ong', 'onl', 'online', 'onyourside', 'ooo', 'open', 'oracle', 'orange', 'org', 'organic', 'origins', 'osaka', 'otsuka', 'ott', 'ovh', 'pa', 'page', 'pamperedchef', 'panasonic', 'panerai', 'paris', 'pars', 'partners', 'parts', 'party', 'passagens', 'pay', 'pccw', 'pe', 'pet', 'pf', 'pfizer', 'pg', 'ph', 'pharmacy', 'phd', 'philips', 'phone', 'photo', 'photography', 'photos', 'physio', 'piaget', 'pics', 'pictet', 'pictures', 'pid', 'pin', 'ping', 'pink', 'pioneer', 'pizza', 'pk', 'pl', 'place', 'play', 'playstation', 'plumbing', 'plus', 'pm', 'pn', 'pnc', 'pohl', 'poker', 'politie', 'porn', 'post', 'pr', 'pramerica', 'praxi', 'press', 'prime', 'pro', 'prod', 'productions', 'prof', 'progressive', 'promo', 'properties', 'property', 'protection', 'pru', 'prudential', 'ps', 'pt', 'pub', 'pw', 'pwc', 'py', 'qa', 'qpon', 'quebec', 'quest', 'qvc', 'racing', 'radio', 'raid', 're', 'read', 'realestate', 'realtor', 'realty', 'recipes', 'red', 'redstone', 'redumbrella', 'rehab', 'reise', 'reisen', 'reit', 'reliance', 'ren', 'rent', 'rentals', 'repair', 'report', 'republican', 'rest', 'restaurant', 'review', 'reviews', 'rexroth', 'rich', 'richardli', 'ricoh', 'rightathome', 'ril', 'rio', 'rip', 'rmit', 'ro', 'rocher', 'rocks', 'rodeo', 'rogers', 'room', 'rs', 'rsvp', 'ru', 'rugby', 'ruhr', 'run', 'rw', 'rwe', 'ryukyu', 'sa', 'saarland', 'safe', 'safety', 'sakura', 'sale', 'salon', 'samsclub', 'samsung', 'sandvik', 'sandvikcoromant', 'sanofi', 'sap', 'sapo', 'sarl', 'sas', 'save', 'saxo', 'sb', 'sbi', 'sbs', 'sc', 'sca', 'scb', 'schaeffler', 'schmidt', 'scholarships', 'school', 'schule', 'schwarz', 'science', 'scjohnson', 'scor', 'scot', 'sd', 'se', 'search', 'seat', 'secure', 'security', 'seek', 'select', 'sener', 'services', 'ses', 'seven', 'sew', 'sex', 'sexy', 'sfr', 'sg', 'sh', 'shangrila', 'sharp', 'shaw', 'shell', 'shia', 'shiksha', 'shoes', 'shop', 'shopping', 'shouji', 'show', 'showtime', 'shriram', 'si', 'silk', 'sina', 'singles', 'site', 'sj', 'sk', 'ski', 'skin', 'sky', 'skype', 'sl', 'sling', 'sm', 'smart', 'smile', 'sn', 'sncf', 'so', 'soccer', 'social', 'softbank', 'software', 'sohu', 'solar', 'solutions', 'song', 'sony', 'soy', 'space', 'spiegel', 'spot', 'spreadbetting', 'sr', 'srl', 'srt', 'st', 'stada', 'staples', 'star', 'starhub', 'statebank', 'statefarm', 'statoil', 'stc', 'stcgroup', 'stockholm', 'storage', 'store', 'stream', 'studio', 'study', 'style', 'su', 'sucks', 'supplies', 'supply', 'support', 'surf', 'surgery', 'suzuki', 'sv', 'swatch', 'swiftcover', 'swiss', 'sx', 'sy', 'sydney', 'symantec', 'systems', 'sz', 'tab', 'taipei', 'talk', 'taobao', 'target', 'tatamotors', 'tatar', 'tattoo', 'tax', 'taxi', 'tc', 'tci', 'td', 'tdk', 'team', 'tech', 'technology', 'tel', 'telecity', 'telefonica', 'temasek', 'tennis', 'teva', 'tf', 'tg', 'th', 'thd', 'theater', 'theatre', 'tiaa', 'tickets', 'tienda', 'tiffany', 'tips', 'tires', 'tirol', 'tj', 'tjmaxx', 'tjx', 'tk', 'tkmaxx', 'tl', 'tm', 'tmall', 'tn', 'to', 'today', 'tokyo', 'tools', 'top', 'toray', 'toshiba', 'total', 'tours', 'town', 'toyota', 'toys', 'tr', 'trade', 'trading', 'training', 'travel', 'travelchannel', 'travelers', 'travelersinsurance', 'trust', 'trv', 'tt', 'tube', 'tui', 'tunes', 'tushu', 'tv', 'tvs', 'tw', 'tz', 'ua', 'ubank', 'ubs', 'uconnect', 'ug', 'uk', 'unicom', 'university', 'uno', 'uol', 'ups', 'us', 'uy', 'uz', 'va', 'vacations', 'vana', 'vanguard', 'vc', 've', 'vegas', 'ventures', 'verisign', 'versicherung', 'vet', 'vg', 'vi', 'viajes', 'video', 'vig', 'viking', 'villas', 'vin', 'vip', 'virgin', 'visa', 'vision', 'vista', 'vistaprint', 'viva', 'vivo', 'vlaanderen', 'vn', 'vodka', 'volkswagen', 'volvo', 'vote', 'voting', 'voto', 'voyage', 'vu', 'vuelos', 'wales', 'walmart', 'walter', 'wang', 'wanggou', 'warman', 'watch', 'watches', 'weather', 'weatherchannel', 'webcam', 'weber', 'website', 'wed', 'wedding', 'weibo', 'weir', 'wf', 'whoswho', 'wien', 'wiki', 'williamhill', 'win', 'windows', 'wine', 'winners', 'wme', 'wolterskluwer', 'woodside', 'work', 'works', 'world', 'wow', 'ws', 'wtc', 'wtf', 'xbox', 'xerox', 'xfinity', 'xihuan', 'xin', 'xn--11b4c3d', 'xn--1ck2e1b', 'xn--1qqw23a', 'xn--2scrj9c', 'xn--30rr7y', 'xn--3bst00m', 'xn--3ds443g', 'xn--3e0b707e', 'xn--3hcrj9c', 'xn--3oq18vl8pn36a', 'xn--3pxu8k', 'xn--42c2d9a', 'xn--45br5cyl', 'xn--45brj9c', 'xn--45q11c', 'xn--4gbrim', 'xn--54b7fta0cc', 'xn--55qw42g', 'xn--55qx5d', 'xn--5su34j936bgsg', 'xn--5tzm5g', 'xn--6frz82g', 'xn--6qq986b3xl', 'xn--80adxhks', 'xn--80ao21a', 'xn--80aqecdr1a', 'xn--80asehdb', 'xn--80aswg', 'xn--8y0a063a', 'xn--90a3ac', 'xn--90ae', 'xn--90ais', 'xn--9dbq2a', 'xn--9et52u', 'xn--9krt00a', 'xn--b4w605ferd', 'xn--bck1b9a5dre4c', 'xn--c1avg', 'xn--c2br7g', 'xn--cck2b3b', 'xn--cg4bki', 'xn--clchc0ea0b2g2a9gcd', 'xn--czr694b', 'xn--czrs0t', 'xn--czru2d', 'xn--d1acj3b', 'xn--d1alf', 'xn--e1a4c', 'xn--eckvdtc9d', 'xn--efvy88h', 'xn--estv75g', 'xn--fct429k', 'xn--fhbei', 'xn--fiq228c5hs', 'xn--fiq64b', 'xn--fiqs8s', 'xn--fiqz9s', 'xn--fjq720a', 'xn--flw351e', 'xn--fpcrj9c3d', 'xn--fzc2c9e2c', 'xn--fzys8d69uvgm', 'xn--g2xx48c', 'xn--gckr3f0f', 'xn--gecrj9c', 'xn--gk3at1e', 'xn--h2breg3eve', 'xn--h2brj9c', 'xn--h2brj9c8c', 'xn--hxt814e', 'xn--i1b6b1a6a2e', 'xn--imr513n', 'xn--io0a7i', 'xn--j1aef', 'xn--j1amh', 'xn--j6w193g', 'xn--jlq61u9w7b', 'xn--jvr189m', 'xn--kcrx77d1x4a', 'xn--kprw13d', 'xn--kpry57d', 'xn--kpu716f', 'xn--kput3i', 'xn--l1acc', 'xn--lgbbat1ad8j', 'xn--mgb9awbf', 'xn--mgba3a3ejt', 'xn--mgba3a4f16a', 'xn--mgba7c0bbn0a', 'xn--mgbaakc7dvf', 'xn--mgbaam7a8h', 'xn--mgbab2bd', 'xn--mgbai9azgqp6j', 'xn--mgbayh7gpa', 'xn--mgbb9fbpob', 'xn--mgbbh1a', 'xn--mgbbh1a71e', 'xn--mgbc0a9azcg', 'xn--mgbca7dzdo', 'xn--mgberp4a5d4ar', 'xn--mgbgu82a', 'xn--mgbi4ecexp', 'xn--mgbpl2fh', 'xn--mgbt3dhd', 'xn--mgbtx2b', 'xn--mgbx4cd0ab', 'xn--mix891f', 'xn--mk1bu44c', 'xn--mxtq1m', 'xn--ngbc5azd', 'xn--ngbe9e0a', 'xn--ngbrx', 'xn--node', 'xn--nqv7f', 'xn--nqv7fs00ema', 'xn--nyqy26a', 'xn--o3cw4h', 'xn--ogbpf8fl', 'xn--p1acf', 'xn--p1ai', 'xn--pbt977c', 'xn--pgbs0dh', 'xn--pssy2u', 'xn--q9jyb4c', 'xn--qcka1pmc', 'xn--qxam', 'xn--rhqv96g', 'xn--rovu88b', 'xn--rvc1e0am3e', 'xn--s9brj9c', 'xn--ses554g', 'xn--t60b56a', 'xn--tckwe', 'xn--tiq49xqyj', 'xn--unup4y', 'xn--vermgensberater-ctb', 'xn--vermgensberatung-pwb', 'xn--vhquv', 'xn--vuq861b', 'xn--w4r85el8fhu5dnra', 'xn--w4rs40l', 'xn--wgbh1c', 'xn--wgbl6a', 'xn--xhq521b', 'xn--xkc2al3hye2a', 'xn--xkc2dl3a5ee0h', 'xn--y9a3aq', 'xn--yfro4i67o', 'xn--ygbi2ammx', 'xn--zfr164b', 'xperia', 'xxx', 'xyz', 'yachts', 'yahoo', 'yamaxun', 'yandex', 'ye', 'yodobashi', 'yoga', 'yokohama', 'you', 'youtube', 'yt', 'yun', 'za', 'zappos', 'zara', 'zero', 'zip', 'zippo', 'zm', 'zone', 'zuerich', 'zw'];

// Array of the available zone types
$server_types = ['MASTER', 'SLAVE', 'NATIVE'];

// $rtypes - array of possible record types
$rtypes = ['A', 'A6', 'AAAA', 'AFSDB', 'ALIAS', 'CAA', 'CDNSKEY', 'CDS', 'CERT', 'CNAME', 'DHCID', 'DLV', 'DNSKEY', 'DNAME', 'DS', 'EUI48', 'EUI64', 'HINFO', 'IPSECKEY', 'KEY', 'KX', 'LOC', 'MAILA', 'MAILB', 'MINFO', 'MR', 'MX', 'NAPTR', 'NS', 'NSEC', 'NSEC3', 'NSEC3PARAM', 'OPENPGPKEY', 'OPT', 'PTR', 'RKEY', 'RP', 'RRSIG', 'SIG', 'SOA', 'SPF', 'SRV', 'SSHFP', 'TLSA', 'TKEY', 'TSIG', 'TXT', 'WKS', 'URI'];

// If fancy records is enabled, extend this field.
if (isset($dns_fancy) && $dns_fancy) {
	$rtypes[14] = 'URL';
	$rtypes[15] = 'MBOXFW';
	$rtypes[16] = 'CURL';
}

if (!function_exists('_')) {
/**
 * @param $text
 * @return mixed
 */
function _($text) {
	return $text;
}
}
/**
 * @return \MyDb\Mdb2\Db
 */
function get_db_mdb2() {
	global $db_mdb2;
	if (!isset($db_mdb2) || !is_object($db_mdb2))
		$db_mdb2 = new db_mdb2(POWERDNS_DB, POWERDNS_USER, POWERDNS_PASSWORD, POWERDNS_HOST);
	return $db_mdb2;
}


/** Get Zone Name from Zone ID
 *
 * @param int $zid Zone ID
 *
 * @return string|bool Domain name
 */
function get_zone_name_from_id($zid) {
	$db_mdb2 = get_db_mdb2();
	if (is_numeric($zid)) {
		$result = $db_mdb2->queryRow('SELECT name FROM domains WHERE id=' . $db_mdb2->quote($zid, 'integer'));
		if ($result) {
			return $result['name'];
		} else {
			error(sprintf('Zone does not exist.'));
		}
	} else {
		error(sprintf(ERR_INV_ARGC, 'get_zone_name_from_id', "Not a valid domainid: $zid"));
	}
	return false;
}

/**
 * @param $needle
 * @param $haystack
 * @return bool
 */
function endsWith($needle, $haystack) {
	$length = mb_strlen($haystack);
	$nLength = mb_strlen($needle);
	return $nLength <= $length && strncmp(mb_substr($haystack, -$nLength), $needle, $nLength) === 0;
}

/**
 * is_valid_email()
 * @param mixed $address
 * @return bool
 */
function is_valid_email($address) {
	$fields = explode('@', $address, 2);
	if ((!preg_match('/^[0-9a-z]([-_.]?[0-9a-z])*$/i', $fields[0])) || (!isset($fields[1]) || $fields[1] == '' || !is_valid_hostname_fqdn($fields[1], 0)))
		return false;
	return true;
}

/** Retrieve all supported dns record types
 *
 * This function might be deprecated.
 *
 * @return string[] array of types
 */
function get_record_types() {
	global $rtypes;
	return $rtypes;
}

if (!function_exists('set_timezone')) {
// Set timezone (required for PHP5)
/**
 * set_timezone()
 * @return void
 */
function set_timezone() {
	global $timezone;
	if (function_exists('date_default_timezone_set')) {
		if (isset($timezone)) {
			date_default_timezone_set($timezone);
		} elseif (!ini_get('date.timezone')) {
				date_default_timezone_set('America/New_York');
			}
	}
}
}
/**
 * isError()
 *
 * @param mixed $result
 * @return bool|void
 */
function isError($result) {
	require_once 'PEAR.php';
	return PEAR::isError($result);
}

/** Get SOA record content for Zone ID
 *
 * @param int $zone_id Zone ID
 * @return string SOA content
 */
function get_soa_record($zone_id) {
	$db_mdb2 = get_db_mdb2();
	$sqlq = 'SELECT content FROM records WHERE type = ' . $db_mdb2->quote('SOA', 'text') . ' AND domain_id = ' . $db_mdb2->quote($zone_id, 'integer');
	$result = $db_mdb2->queryOne($sqlq);
	return $result;
}

/** Get SOA Serial Number
 *
 * @param string $soa_rec SOA record content
 * @return string SOA serial
 */
function get_soa_serial($soa_rec) {
	$soa = explode(' ', $soa_rec);
	return $soa[2];
}

/** Get Next Date
 *
 * @param string $curr_date Current date in YYYYMMDD format
 * @return string Date +1 day
 */
function get_next_date($curr_date) {
	$next_date = date('Ymd', strtotime('+1 day', strtotime($curr_date)));
	return $next_date;
}

/** Get Next Serial
 *
 * Zone transfer to zone slave(s) will occur only if the serial number
 * of the SOA RR is arithmetically greater that the previous one
 * (as defined by RFC-1982).
 *
 * The serial should be updated, unless:
 *
 * - the serial is set to "0", see http://doc.powerdns.com/types.html#id482176
 *
 * - set a fresh serial ONLY if the existing serial is lower than the current date
 *
 * - update date in serial if it reaches limit of revisions for today or do you
 * think that ritual suicide is better in such case?
 *
 * "This works unless you will require to make more than 99 changes until the new
 * date is reached - in which case perhaps ritual suicide is the best option."
 * http://www.zytrax.com/books/dns/ch9/serial.html
 *
 * @param string $curr_serial Current Serial No
 * @param string $today Optional date for "today"
 *
 * @return string Next serial number
 */
function get_next_serial($curr_serial, $today = '') {
	// Autoserial
	if ($curr_serial == 0)
		return 0;
	// Serial number could be a not date based
	if ($curr_serial < 1979999999)
		return $curr_serial+1;
	// Reset the serial number, Bind was written in the early 1980s
	if ($curr_serial == 1979999999)
		return 1;
	if ($today == '') {
		set_timezone();
		$today = date('Ymd');
	}
	$revision = (int) mb_substr($curr_serial, -2);
	$ser_date = mb_substr($curr_serial, 0, 8);
	if ($curr_serial == '0')
		$serial = $curr_serial;
	elseif ($curr_serial == $today . '99')
		$serial = get_next_date($today) . '00';
	else {
		if (strcmp($today, $ser_date) === 0)
			// Current serial starts with date of today, so we need to update the revision only.
			++$revision;
		elseif (strncmp($today, $curr_serial, 8) === -1) {
			// Reuse existing serial date if it's in the future
			$today = mb_substr($curr_serial, 0, 8);
			// Get next date if revision reaches maximum per day (99) limit otherwise increment the counter
			if ($revision == 99) {
				$today = get_next_date($today);
				$revision = '00';
			} else
				++$revision;
		} else {
			// Current serial did not start of today, so it's either an older
			// serial, therefore set a fresh serial
			$revision = '00';
		}
		// Create new serial out of existing/updated date and revision
		$serial = $today . str_pad($revision, 2, '0', STR_PAD_LEFT);
	}
	return $serial;
}

/** Set SOA serial in SOA content
 *
 * @param string $soa_rec SOA record content
 * @param string $serial New serial number
 * @return string Updated SOA record
 */
function set_soa_serial($soa_rec, $serial) {
	// Split content of current SOA record into an array.
	$soa = explode(' ', $soa_rec);
	$soa[2] = $serial;
	// Build new SOA record content
	$soa_rec = implode(' ', $soa);
	rtrim($soa_rec);
	return $soa_rec;
}

/** Update SOA record
 *
 * @param int $domain_id Domain ID
 * @param string $content SOA content to set
 *
 * @return boolean true if success
 */
function update_soa_record($domain_id, $content) {
	$db_mdb2 = get_db_mdb2();

	$sqlq = 'UPDATE records SET content = ' . $db_mdb2->quote($content, 'text') . ' WHERE domain_id = ' . $db_mdb2->quote($domain_id, 'integer') . ' AND type = ' . $db_mdb2->quote('SOA', 'text');
	$response = $db_mdb2->query($sqlq);

	if (isError($response)) {
		error($response->getMessage());
		return false;
	}

	return true;
}

/** Update SOA serial
 *
 * Increments SOA serial to next possible number
 *
 * @param int $domain_id Domain ID
 * @return boolean true if success
 */
function update_soa_serial($domain_id) {
	$soa_rec = get_soa_record($domain_id);
	if ($soa_rec == NULL)
		return false;
	$curr_serial = get_soa_serial($soa_rec);
	$new_serial = get_next_serial($curr_serial);
	if ($curr_serial != $new_serial) {
		$soa_rec = set_soa_serial($soa_rec, $new_serial);
		return update_soa_record($domain_id, $soa_rec);
	}
	return true;
}


/** Validate DNS record input
 *
 * @param int $rid Record ID
 * @param int $zid Zone ID
 * @param string $type Record Type
 * @param mixed $content content part of record
 * @param mixed $name Name part of record
 * @param mixed $prio Priority
 * @param mixed $ttl TTL
 *
 * @return boolean true on success, false otherwise
 */
function validate_input($rid, $zid, $type, &$content, &$name, &$prio, &$ttl) {

	$zone = get_zone_name_from_id($zid);    // TODO check for return

	if (!endsWith(strtolower($zone), strtolower($name))) {
		if (isset($name) && $name != '') {
			$name = $name . '.' . $zone;
		} else {
			$name = $zone;
		}
	}

	switch ($type) {

	  case 'A':
		if (!is_valid_ipv4($content))
			  return false;
		if (!is_valid_rr_cname_exists($name, $rid))
			  return false;
		if (!is_valid_hostname_fqdn($name, 1))
			  return false;
	  break;

	  case 'A6': // TODO: implement validation.
	  break;

	  case 'AAAA':
		if (!is_valid_ipv6($content))
			  return false;
		if (!is_valid_rr_cname_exists($name, $rid))
			  return false;
		if (!is_valid_hostname_fqdn($name, 1))
			  return false;
	  break;

	  case 'AFSDB': // TODO: implement validation.
	  break;

	  case 'ALIAS': // TODO: implement validation.
	  break;

	  case 'CAA': // TODO: implement validation.
	  break;

	  case 'CDNSKEY': // TODO: implement validation.
	  break;

	  case 'CDS': // TODO: implement validation.
	  break;

	  case 'CERT': // TODO: implement validation.
	  break;

	  case 'CNAME':
		if (!is_valid_rr_cname_name($name))
			  return false;
		if (!is_valid_rr_cname_unique($name, $rid))
			  return false;
		if (!is_valid_hostname_fqdn($name, 1))
			  return false;
		if (!is_valid_hostname_fqdn($content, 0))
			  return false;
		if (!is_not_empty_cname_rr($name, $zone))
			  return false;
		break;

	  case 'DHCID': // TODO: implement validation
	  break;

	  case 'DLV': // TODO: implement validation
	  break;

	  case 'DNAME': // TODO: implement validation.
	  break;

	  case 'DNSKEY': // TODO: implement validation
	  break;

	  case 'DS': // TODO: implement validation
	  break;

	  case 'EUI48': // TODO: implement validation
	  break;

	  case 'EUI64': // TODO: implement validation
	  break;

	  case 'HINFO':
		if (!is_valid_rr_hinfo_content($content))
			  return false;
		if (!is_valid_hostname_fqdn($name, 1))
			  return false;
	  break;

	  case 'IPSECKEY': // TODO: implement validation
	  break;

	  case 'KEY': // TODO: implement validation
	  break;

	  case 'KX': // TODO: implement validation
	  break;

	  case 'LOC':
		if (!is_valid_loc($content))
			  return false;
		if (!is_valid_hostname_fqdn($name, 1))
			  return false;
	  break;

	  case 'MAILA': // TODO: implement validation.
	  break;

	  case 'MAILB': // TODO: implement validation.
	  break;

	  case 'MINFO': // TODO: implement validation
	  break;

	  case 'MR': // TODO: implement validation
	  break;

	  case 'MX':
		if (!is_valid_hostname_fqdn($content, 0))
			  return false;
		if (!is_valid_hostname_fqdn($name, 1))
			  return false;
		if (!is_valid_non_alias_target($content))
			  return false;
	  break;

	  case 'NAPTR': // TODO: implement validation
	  break;

	  case 'NS':
		if (!is_valid_hostname_fqdn($content, 0))
			  return false;
		if (!is_valid_hostname_fqdn($name, 1))
			  return false;
		if (!is_valid_non_alias_target($content))
			  return false;
	  break;

	  case 'NSEC': // TODO: implement validation
	  break;

	  case 'NSEC3': // TODO: implement validation
	  break;

	  case 'NSEC3PARAM': // TODO: implement validation
	  break;

	  case 'OPENPGPKEY': // TODO: implement validation.
	  break;

	  case 'OPT': // TODO: implement validation
	  break;

	  case 'PTR':
		if (!is_valid_hostname_fqdn($content, 0))
			  return false;
		if (!is_valid_hostname_fqdn($name, 1))
			  return false;
	  break;

	  case 'RKEY': // TODO: implement validation
	  break;

	  case 'RP': // TODO: implement validation
	  break;

	  case 'RRSIG': // TODO: implement validation
	  break;

	  case 'SIG': // TODO: implement validation.
	  break;

	  case 'SOA':
		if (!is_valid_rr_soa_name($name, $zone))
			  return false;
		if (!is_valid_hostname_fqdn($name, 1))
			  return false;
		if (!is_valid_rr_soa_content($content)) {
		  error(ERR_DNS_CONTENT);
		  return false;
		}
	  break;

	  case 'SPF':
		if (!is_valid_spf($content))
			  return false;
	  break;

	  case 'SRV':
		if (!is_valid_rr_srv_name($name))
			  return false;
		if (!is_valid_rr_srv_content($content))
			  return false;
	  break;

	  case 'SSHFP': // TODO: implement validation
	  break;

	  case 'TKEY': // TODO: implement validation.
	  break;

	  case 'TLSA': // TODO: implement validation
	  break;

	  case 'TSIG': // TODO: implement validation
	  break;

	  case 'TXT':
		if (!is_valid_printable($name))
			  return false;
		if (!is_valid_printable($content))
			  return false;
	  break;

	  case 'WKS': // TODO: implement validation
	  break;

	  case 'MBOXFW': // TODO: implement validation
	  break;

	  case 'URL': // TODO: implement validation.
	  break;

	  default:
		error(ERR_DNS_RR_TYPE);
		return false;
	}

	if (!is_valid_rr_prio($prio, $type))
		return false;
	if (!is_valid_rr_ttl($ttl))
		return false;

	return true;
}

/** Test if hostname is valid FQDN
 *
 * @param mixed $hostname Hostname string
 * @param string $wildcard Hostname includes wildcard '*'
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_hostname_fqdn(&$hostname, $wildcard) {
	global $dns_top_level_tld_check;
	global $dns_strict_tld_check;
	global $valid_tlds;

	$hostname = preg_replace("/\.$/", '', $hostname);

	# The full domain name may not exceed a total length of 253 characters.
	if (strlen($hostname) > 253) {
		error(ERR_DNS_HN_TOO_LONG);
		return false;
	}

	$hostname_labels = explode('.', $hostname);
	$label_count = count($hostname_labels);

	if ($dns_top_level_tld_check && $label_count == 1)
		return false;

	foreach ($hostname_labels as $hostname_label) {
		if ($wildcard == 1 && !isset($first)) {
			if (!preg_match('/^(\*|[\w-\/]+)$/', $hostname_label)) {
				error(ERR_DNS_HN_INV_CHARS);
				return false;
			}
			$first = 1;
		} else {
			if (!preg_match('/^[\w-\/]+$/', $hostname_label)) {
				error(ERR_DNS_HN_INV_CHARS);
				return false;
			}
		}
		if (substr($hostname_label, 0, 1) == '-') {
			error(ERR_DNS_HN_DASH);
			return false;
		}
		if (substr($hostname_label, -1, 1) == '-') {
			error(ERR_DNS_HN_DASH);
			return false;
		}
		if ('' === $hostname_label || strlen($hostname_label) > 63) {
			error(ERR_DNS_HN_LENGTH);
			return false;
		}
	}

	if ($hostname_labels[$label_count - 1] == 'arpa' && (substr_count($hostname_labels[0], '/') == 1 XOR substr_count($hostname_labels[1], '/') == 1)) {
		if (substr_count($hostname_labels[0], '/') == 1) {
			$array = explode('/', $hostname_labels[0]);
		} else {
			$array = explode('/', $hostname_labels[1]);
		}
		if (count($array) != 2) {
			error(ERR_DNS_HOSTNAME);
			return false;
		}
		if (!is_numeric($array[0]) || $array[0] < 0 || $array[0] > 255) {
			error(ERR_DNS_HOSTNAME);
			return false;
		}
		if (!is_numeric($array[1]) || $array[1] < 25 || $array[1] > 31) {
			error(ERR_DNS_HOSTNAME);
			return false;
		}
	} else {
		if (substr_count($hostname, '/') > 0) {
			error(ERR_DNS_HN_SLASH);
			return false;
		}
	}

	if ($dns_strict_tld_check && !in_array(strtolower($hostname_labels[$label_count - 1]), $valid_tlds)) {
		error(ERR_DNS_INV_TLD);
		return false;
	}

	return true;
}

/** Test if IPv4 address is valid
 *
 * @param string $ipv4 IPv4 address string
 * @param boolean $answer print error if true
 * [default=true]
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_ipv4($ipv4, $answer = true) {

  if(filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE) {
	if($answer) {
	  error(ERR_DNS_IPV4);
	}
	return false;
  }

  return true;
}

/** Test if IPv6 address is valid
 *
 * @param string $ipv6 IPv6 address string
 * @param boolean $answer print error if true
 * [default=true]
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_ipv6($ipv6, $answer = true) {

  if(filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE) {
	if($answer) {
	  error(ERR_DNS_IPV6);
	}
	return false;
  }

  return true;
}

/** Test if multiple IP addresses are valid
 *
 *  Takes a string of comma seperated IP addresses and tests validity
 *
 *  @param string $ips Comma seperated IP addresses
 *
 *  @return boolean true if valid, false otherwise
 */
function are_multipe_valid_ips($ips) {

// multiple master NS-records are permitted and must be separated by ,
// eg. "192.0.0.1, 192.0.0.2, 2001:1::1"

	$are_valid = false;
	$multiple_ips = explode(',', $ips);
	if (is_array($multiple_ips)) {
		foreach ($multiple_ips as $m_ip) {
			$trimmed_ip = trim($m_ip);
			if (is_valid_ipv4($trimmed_ip, false) || is_valid_ipv6($trimmed_ip, true)) {
				$are_valid = true;
			} else {
				// as soon there is an invalid ip-addr
				// exit and return false
				echo "hin:=$trimmed_ip=";
				return false;
			}
		}
	} elseif (is_valid_ipv4($ips) || is_valid_ipv6($ips)) {
		$are_valid = true;
	}
	if ($are_valid) {
		return true;
	} else {
		return false;
	}
}

/** Test if string is printable
 *
 * @param string $string string
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_printable($string) {
	if (!preg_match('/^[[:print:]]+$/', trim($string))) {
		error(ERR_DNS_PRINTABLE);
		return false;
	}
	return true;
}

/** Test if CNAME is valid
 *
 * Check if any MX or NS entries exist which invalidated CNAME
 *
 * @param string $name CNAME to lookup
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_rr_cname_name($name) {
	$db_mdb2 = get_db_mdb2();

	$query = 'SELECT id FROM records
			WHERE content = ' . $db_mdb2->quote($name, 'text') . '
			AND (type = ' . $db_mdb2->quote('MX', 'text') . ' OR type = ' . $db_mdb2->quote('NS', 'text') . ')';

	$response = $db_mdb2->queryOne($query);

	if (!empty($response)) {
		error(ERR_DNS_CNAME);
		return false;
	}

	return true;
}

/** Check if CNAME already exists
 *
 * @param string $name CNAME
 * @param int $rid Record ID
 *
 * @return boolean true if non-existant, false if exists
 */
function is_valid_rr_cname_exists($name, $rid) {
	$db_mdb2 = get_db_mdb2();

	$where = ($rid > 0 ? ' AND id != ' . $db_mdb2->quote($rid, 'integer') : '');
	$query = 'SELECT id FROM records
						WHERE name = ' . $db_mdb2->quote($name, 'text') . $where . "
						AND TYPE = 'CNAME'";

	$response = $db_mdb2->queryOne($query);
	if ($response) {
		error(ERR_DNS_CNAME_EXISTS);
		return false;
	}
	return true;
}

/** Check if CNAME is unique (doesn't overlap A/AAAA)
 *
 * @param string $name CNAME
 * @param string $rid Record ID
 *
 * @return boolean true if unique, false if duplicate
 */
function is_valid_rr_cname_unique($name, $rid) {
	$db_mdb2 = get_db_mdb2();

	$where = ($rid > 0 ? ' AND id != ' . $db_mdb2->quote($rid, 'integer') : '');
	$query = 'SELECT id FROM records
						WHERE name = ' . $db_mdb2->quote($name, 'text') . $where . "
						AND TYPE IN ('A', 'AAAA', 'CNAME')";

	$response = $db_mdb2->queryOne($query);
	if ($response) {
		error(ERR_DNS_CNAME_UNIQUE);
		return false;
	}
	return true;
}

/**
 * Check that the zone does not have a empty CNAME RR
 *
 * @param string $name
 * @param string $zone
 * @return bool
 */
function is_not_empty_cname_rr($name, $zone) {

	if ($name == $zone) {
		error(ERR_DNS_CNAME_EMPTY);
		return false;
	}
	return true;
}

/** Check if target is not a CNAME
 *
 * @param string $target target to check
 *
 * @return boolean true if not alias, false if CNAME exists
 */
function is_valid_non_alias_target($target) {
	$db_mdb2 = get_db_mdb2();

	$query = 'SELECT id FROM records
			WHERE name = ' . $db_mdb2->quote($target, 'text') . '
			AND TYPE = ' . $db_mdb2->quote('CNAME', 'text');

	$response = $db_mdb2->queryOne($query);
	if ($response) {
		error(ERR_DNS_NON_ALIAS_TARGET);
		return false;
	}
	return true;
}

/** Check if HINFO content is valid
 *
 * @param string $content HINFO record content
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_rr_hinfo_content($content) {

	if ($content[0] == '"') {
		$fields = preg_split('/(?<=") /', $content, 2);
	} else {
		$fields = explode(' ', $content, 2);
	}

	for ($i = 0; $i < 2; $i++) {
		if (!preg_match("/^([^\s]{1,1000})|\"([^\"]{1,998}\")$/i", $fields[$i])) {
			error(ERR_DNS_HINFO_INV_CONTENT);
			return false;
		}
	}

	return true;
}

/** Check if SOA content is valid
 *
 * @param mixed $content SOA record content
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_rr_soa_content(&$content) {

	$fields = preg_split("/\s+/", trim($content));
	$field_count = count($fields);

	if ($field_count == 0 || $field_count > 7) {
		return false;
	} else {
		if (!is_valid_hostname_fqdn($fields[0], 0) || preg_match('/\.arpa\.?$/', $fields[0]))
			return false;
		$final_soa = $fields[0];

		if (isset($fields[1])) {
			$addr_input = $fields[1];
		} else {
			global $dns_hostmaster;
			$addr_input = $dns_hostmaster;
		}
		if (!preg_match('/@/', $addr_input)) {
			$addr_input = preg_split('/(?<!\\\)\./', $addr_input, 2);
			$addr_to_check = str_replace("\\", '', $addr_input[0]) . '@' . $addr_input[1];
		} else {
			$addr_to_check = $addr_input;
		}

		if (!is_valid_email($addr_to_check)) {
			return false;
		} else {
			$addr_final = explode('@', $addr_to_check, 2);
			$final_soa .= ' ' . str_replace('.', "\\.", $addr_final[0]) . '.' . $addr_final[1];
		}

		if (isset($fields[2])) {
			if (!is_numeric($fields[2]))
				return false;
			$final_soa .= ' ' . $fields[2];
		} else {
			$final_soa .= ' 0';
		}

		if ($field_count != 7) {
			return false;
		} else {
			for ($i = 3; $i < 7; $i++) {
				if (!is_numeric($fields[$i])) {
					return false;
				} else {
					$final_soa .= ' ' . $fields[$i];
				}
			}
		}
	}
	$content = $final_soa;
	return true;
}

/** Check if SOA name is valid
 *
 * Checks if SOA name = zone name
 *
 * @param string $name SOA name
 * @param string $zone Zone name
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_rr_soa_name($name, $zone) {
	if ($name != $zone) {
		error(ERR_DNS_SOA_NAME);
		return false;
	}
	return true;
}

/** Check if Priority is valid
 *
 * Check if MX or SRV priority is within range, otherwise set to 0
 *
 * @param mixed $prio Priority
 * @param string $type Record type
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_rr_prio(&$prio, $type) {
	if ($type == 'MX' || $type == 'SRV') {
		if (!is_numeric($prio) || $prio < 0 || $prio > 65535) {
			error(ERR_DNS_INV_PRIO);
			return false;
		}
	} else {
		$prio = 0;
	}

	return true;
}

/** Check if SRV name is valid
 *
 * @param mixed $name SRV name
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_rr_srv_name(&$name) {

	if (strlen($name) > 255) {
		error(ERR_DNS_HN_TOO_LONG);
		return false;
	}

	$fields = explode('.', $name, 3);
	if (!preg_match('/^_[\w-]+$/i', $fields[0])) {
		error(ERR_DNS_SRV_NAME_SERVICE, $name);
		return false;
	}
	if (!preg_match('/^_[\w]+$/i', $fields[1])) {
		error(ERR_DNS_SRV_NAME_PROTO, $name);
		return false;
	}
	if (!is_valid_hostname_fqdn($fields[2], 0)) {
		error(ERR_DNS_SRV_NAME, $name);
		return false;
	}
	$name = implode('.', $fields);
	return true;
}

/** Check if SRV content is valid
 *
 * @param mixed $content SRV content
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_rr_srv_content(&$content) {
	$fields = preg_split("/\s+/", trim($content), 3);
	if (!is_numeric($fields[0]) || $fields[0] < 0 || $fields[0] > 65535) {
		error(ERR_DNS_SRV_WGHT, $name);
		return false;
	}
	if (!is_numeric($fields[1]) || $fields[1] < 0 || $fields[1] > 65535) {
		error(ERR_DNS_SRV_PORT, $name);
		return false;
	}
	if ($fields[2] == '' || ($fields[2] != '.' && !is_valid_hostname_fqdn($fields[2], 0))) {
		error(ERR_DNS_SRV_TRGT, $name);
		return false;
	}
	$content = implode(' ', $fields);
	return true;
}

/** Check if TTL is valid and within range
 *
 * @param int $ttl TTL
 *
 * @return boolean true if valid,false otherwise
 */
function is_valid_rr_ttl(&$ttl) {

	if (!isset($ttl) || $ttl == '') {
		global $dns_ttl;
		$ttl = $dns_ttl;
	}

	if (!is_numeric($ttl) || $ttl < 0 || $ttl > 2147483647) {
		error(ERR_DNS_INV_TTL);
		return false;
	}

	return true;
}

/** Check if search string is valid
 *
 * @param string $search_string search string
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_search($search_string) {

	// Only allow for alphanumeric, numeric, dot, dash, underscore and
	// percent in search string. The last two are wildcards for SQL.
	// Needs extension probably for more usual record types.

	return preg_match('/^[a-z0-9.\-%_]+$/i', $search_string);
}

/** Check if SPF content is valid
 *
 * @param string $content SPF content
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_spf($content) {
	//Regex from http://www.schlitt.net/spf/tests/spf_record_regexp-03.txt
	$regex = "^[Vv]=[Ss][Pp][Ff]1( +([-+?~]?([Aa][Ll][Ll]|[Ii][Nn][Cc][Ll][Uu][Dd][Ee]:(%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\})|[Aa](:(%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\}))?((/([1-9]|1[0-9]|2[0-9]|3[0-2]))?(//([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8]))?)?|[Mm][Xx](:(%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\}))?((/([1-9]|1[0-9]|2[0-9]|3[0-2]))?(//([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8]))?)?|[Pp][Tt][Rr](:(%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\}))?|[Ii][Pp]4:([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(/([1-9]|1[0-9]|2[0-9]|3[0-2]))?|[Ii][Pp]6:(::|([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4}|([0-9A-Fa-f]{1,4}:){1,8}:|([0-9A-Fa-f]{1,4}:){7}:[0-9A-Fa-f]{1,4}|([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}){1,2}|([0-9A-Fa-f]{1,4}:){5}(:[0-9A-Fa-f]{1,4}){1,3}|([0-9A-Fa-f]{1,4}:){4}(:[0-9A-Fa-f]{1,4}){1,4}|([0-9A-Fa-f]{1,4}:){3}(:[0-9A-Fa-f]{1,4}){1,5}|([0-9A-Fa-f]{1,4}:){2}(:[0-9A-Fa-f]{1,4}){1,6}|[0-9A-Fa-f]{1,4}:(:[0-9A-Fa-f]{1,4}){1,7}|:(:[0-9A-Fa-f]{1,4}){1,8}|([0-9A-Fa-f]{1,4}:){6}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|([0-9A-Fa-f]{1,4}:){6}:([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|[0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|::([0-9A-Fa-f]{1,4}:){0,6}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(/([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8]))?|[Ee][Xx][Ii][Ss][Tt][Ss]:(%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\}))|[Rr][Ee][Dd][Ii][Rr][Ee][Cc][Tt]=(%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\})|[Ee][Xx][Pp]=(%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\})|[A-Za-z][-.0-9A-Z_a-z]*=(%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?[Rr]?[+-/=_]*\}|%%|%_|%-|[!-$&-~])*))* *$^";
	if (!preg_match($regex, $content)) {
		return false;
	} else {
		return true;
	}
}

/** Check if LOC content is valid
 *
 * @param string $content LOC content
 *
 * @return boolean true if valid, false otherwise
 */
function is_valid_loc($content) {
	$regex = "^(90|[1-8]\d|0?\d)( ([1-5]\d|0?\d)( ([1-5]\d|0?\d)(\.\d{1,3})?)?)? [NS] (180|1[0-7]\d|[1-9]\d|0?\d)( ([1-5]\d|0?\d)( ([1-5]\d|0?\d)(\.\d{1,3})?)?)? [EW] (-(100000(\.00)?|\d{1,5}(\.\d\d)?)|([1-3]?\d{1,7}(\.\d\d)?|4([01][0-9]{6}|2([0-7][0-9]{5}|8([0-3][0-9]{4}|4([0-8][0-9]{3}|9([0-5][0-9]{2}|6([0-6][0-9]|7[01]))))))(\.\d\d)?|42849672(\.([0-8]\d|9[0-5]))?))[m]?( (\d{1,7}|[1-8]\d{7})(\.\d\d)?[m]?){0,3}$^";
	if (!preg_match($regex, $content)) {
		return false;
	} else {
		return true;
	}
}
