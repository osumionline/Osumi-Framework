<?php
class OBrowser{
  private $browser_data = null;
  private $ua = '';

  public function setUa($ua){
    $this->ua = $ua;
  }

  public function getUa(){
    return $this->ua;
  }

  public function setBrowserdata($bd){
    $this->browser_data = $bd;
  }

  public function getBrowserData(){
    if (is_null($this->browser_data)){
      $this->browser_detection();
    }

    return $this->browser_data;
  }

  public function isIpad() {
    if (is_null($this->getBrowserData())){
      $this->browser_detection();
    }

    $browser_data = $this->getBrowserData();

    if (isset($browser_data['mobile_data'][0]) && $browser_data['mobile_data'][0] == 'ipad'){
      return true;
    }
    else{
      return false;
    }
  }

  public function isIpod() {
    if (is_null($this->getBrowserData())){
      $this->browser_detection();
    }

    $browser_data = $this->getBrowserData();

    if (isset($browser_data['mobile_data'][0]) && $browser_data['mobile_data'][0] == 'ipod'){
      return true;
    }
    else{
      return false;
    }
  }

  public function isIphone(){
    if (is_null($this->getBrowserData())){
      $this->browser_detection();
    }

    $browser_data = $this->getBrowserData();

    if (isset($browser_data['mobile_data'][0]) && $browser_data['mobile_data'][0] == 'iphone'){
      return true;
    }
    else{
      return false;
    }
  }

  public function isAndroid(){
    if (is_null($this->getBrowserData())){
      $this->browser_detection();
    }

    $browser_data = $this->getBrowserData();

    if (isset($browser_data['mobile_data'][3]) && $browser_data['mobile_data'][3] == 'android'){
      return true;
    }
    else{
      return false;
    }
  }

  public function isBlackberry(){
    if (is_null($this->getBrowserData())){
      $this->browser_detection();
    }

    $browser_data = $this->getBrowserData();

    if (isset($browser_data['mobile_data'][0]) && $browser_data['mobile_data'][0] == 'blackberry'){
      return true;
    }
    else{
      return false;
    }
  }

  public function isNokia(){
    if (is_null($this->getBrowserData())){
      $this->browser_detection();
    }

    $browser_data = $this->getBrowserData();

    if (isset($browser_data['mobile_data'][0]) && $browser_data['mobile_data'][0] == 'nokia'){
      return true;
    }
    else{
      return false;
    }
  }

  public function isMobile($ipad=false){
    $is_mobile = $this->isIpod() || $this->isIphone() || $this->isAndroid() || $this->isBlackberry() || $this->isNokia();
    if ($ipad){
      return ($this->isIpad() || $is_mobile);
    }
    else{
      return ($is_mobile);
    }
  }

  function browser_detection()
  {
		//initialize all variables with default values to prevent error
		$a_browser_math_number = '';
		$a_full_assoc_data = '';
		$a_full_data = '';
		$a_mobile_data = '';
		$a_moz_data = '';
		$a_os_data = '';
		$a_unhandled_browser = '';
		$a_webkit_data = '';
		$b_dom_browser = false;
		$b_os_test = true;
		$b_mobile_test = true;
		$b_safe_browser = false;
		$b_success = false;// boolean for if browser found in main test
		$browser_math_number = '';
		$browser_temp = '';
		$browser_working = '';
		$browser_number = '';
		$ie_version = '';
		$mobile_test = '';
		$moz_release_date = '';
		$moz_rv = '';
		$moz_rv_full = '';
		$moz_type = '';
		$moz_number = '';
		$os_number = '';
		$os_type = '';
		$run_time = '';
		$true_ie_number = '';
		$ua_type = 'bot';// default to bot since you never know with bots
		$webkit_type = '';
		$webkit_type_number = '';

    $browser_user_agent = strtolower($this->getUa());

		// known browsers, list will be updated routinely, check back now and then
		$a_browser_types = [
			[ 'opera', true, 'op', 'bro' ],
			[ 'msie', true, 'ie', 'bro' ],
			// webkit before gecko because some webkit ua strings say: like gecko
			[ 'webkit', true, 'webkit', 'bro' ],
			// konq will be using webkit soon
			[ 'konqueror', true, 'konq', 'bro' ],
			// covers Netscape 6-7, K-Meleon, Most linux versions, uses moz array below
			[ 'gecko', true, 'moz', 'bro' ],
			[ 'netpositive', false, 'netp', 'bbro' ],// beos browser
			[ 'lynx', false, 'lynx', 'bbro' ], // command line browser
			[ 'elinks ', false, 'elinks', 'bbro' ], // new version of links
			[ 'elinks', false, 'elinks', 'bbro' ], // alternate id for it
			[ 'links2', false, 'links2', 'bbro' ], // alternate links version
			[ 'links ', false, 'links', 'bbro' ], // old name for links
			[ 'links', false, 'links', 'bbro' ], // alternate id for it
			[ 'w3m', false, 'w3m', 'bbro' ], // open source browser, more features than lynx/links
			[ 'webtv', false, 'webtv', 'bbro' ],// junk ms webtv
			[ 'amaya', false, 'amaya', 'bbro' ],// w3c browser
			[ 'dillo', false, 'dillo', 'bbro' ],// linux browser, basic table support
			[ 'ibrowse', false, 'ibrowse', 'bbro' ],// amiga browser
			[ 'icab', false, 'icab', 'bro' ],// mac browser
			[ 'crazy browser', true, 'ie', 'bro' ],// uses ie rendering engine

			// search engine spider bots:
			[ 'googlebot', false, 'google', 'bot' ],// google
			[ 'mediapartners-google', false, 'adsense', 'bot' ],// google adsense
			[ 'yahoo-verticalcrawler', false, 'yahoo', 'bot' ],// old yahoo bot
			[ 'yahoo! slurp', false, 'yahoo', 'bot' ], // new yahoo bot
			[ 'yahoo-mm', false, 'yahoomm', 'bot' ], // gets Yahoo-MMCrawler and Yahoo-MMAudVid bots
			[ 'inktomi', false, 'inktomi', 'bot' ], // inktomi bot
			[ 'slurp', false, 'inktomi', 'bot' ], // inktomi bot
			[ 'fast-webcrawler', false, 'fast', 'bot' ],// Fast AllTheWeb
			[ 'msnbot', false, 'msn', 'bot' ],// msn search
			[ 'ask jeeves', false, 'ask', 'bot' ], //jeeves/teoma
			[ 'teoma', false, 'ask', 'bot' ],//jeeves teoma
			[ 'scooter', false, 'scooter', 'bot' ],// altavista
			[ 'openbot', false, 'openbot', 'bot' ],// openbot, from taiwan
			[ 'ia_archiver', false, 'ia_archiver', 'bot' ],// ia archiver
			[ 'zyborg', false, 'looksmart', 'bot' ],// looksmart
			[ 'almaden', false, 'ibm', 'bot' ],// ibm almaden web crawler
			[ 'baiduspider', false, 'baidu', 'bot' ],// Baiduspider asian search spider
			[ 'psbot', false, 'psbot', 'bot' ],// psbot image crawler
			[ 'gigabot', false, 'gigabot', 'bot' ],// gigabot crawler
			[ 'naverbot', false, 'naverbot', 'bot' ],// naverbot crawler, bad bot, block
			[ 'surveybot', false, 'surveybot', 'bot' ],//
			[ 'boitho.com-dc', false, 'boitho', 'bot' ],//norwegian search engine
			[ 'objectssearch', false, 'objectsearch', 'bot' ],// open source search engine
			[ 'answerbus', false, 'answerbus', 'bot' ],// http://www.answerbus.com/, web questions
			[ 'sohu-search', false, 'sohu', 'bot' ],// chinese media company, search component
			[ 'iltrovatore-setaccio', false, 'il-set', 'bot' ],

			// various http utility libaries
			[ 'w3c_validator', false, 'w3c', 'lib' ], // uses libperl, make first
			[ 'wdg_validator', false, 'wdg', 'lib' ], //
			[ 'libwww-perl', false, 'libwww-perl', 'lib' ],
			[ 'jakarta commons-httpclient', false, 'jakarta', 'lib' ],
			[ 'python-urllib', false, 'python-urllib', 'lib' ],

			// download apps
			[ 'getright', false, 'getright', 'dow' ],
			[ 'wget', false, 'wget', 'dow' ],// open source downloader, obeys robots.txt

			// netscape 4 and earlier tests, put last so spiders don't get caught
			[ 'mozilla/4.', false, 'ns', 'bbro' ],
			[ 'mozilla/3.', false, 'ns', 'bbro' ],
			[ 'mozilla/2.', false, 'ns', 'bbro' ]
    ];

		/*
		moz types array
		note the order, netscape6 must come before netscape, which  is how netscape 7 id's itself.
		rv comes last in case it is plain old mozilla. firefox/netscape/seamonkey need to be later
		Thanks to: http://www.zytrax.com/tech/web/firefox-history.html
		*/
		$a_moz_types = [ 'bonecho', 'camino', 'epiphany', 'firebird', 'flock', 'galeon', 'iceape', 'icecat', 'k-meleon', 'minimo', 'multizilla', 'phoenix', 'songbird', 'swiftfox', 'seamonkey', 'shiretoko', 'iceweasel', 'firefox', 'minefield', 'netscape6', 'netscape', 'rv' ];

		/*
		webkit types, this is going to expand over time as webkit browsers spread
		konqueror is probably going to move to webkit, so this is preparing for that
		It will now default to khtml. gtklauncher is the temp id for epiphany, might
		change. Defaults to applewebkit, and will all show the webkit number.
		*/
		$a_webkit_types = [ 'arora', 'chrome', 'epiphany', 'gtklauncher', 'konqueror', 'midori', 'omniweb', 'safari', 'uzbl', 'applewebkit', 'webkit' ];

		/*
		run through the browser_types array, break if you hit a match, if no match, assume old browser
		or non dom browser, assigns false value to $b_success.
		*/
		$i_count = count( $a_browser_types );
    for ( $i = 0; $i < $i_count; $i++ )
		{
  		//unpacks browser array, assigns to variables, need to not assign til found in string
  		$browser_temp = $a_browser_types[$i][0];// text string to id browser from array

			if ( strstr( $browser_user_agent, $browser_temp ) )
			{
				/*
				it defaults to true, will become false below if needed
				this keeps it easier to keep track of what is safe, only
				explicit false assignment will make it false.
				*/
				$b_safe_browser = true;
				$browser_name = $browser_temp;// text string to id browser from array

				// assign values based on match of user agent string
				$b_dom_browser = $a_browser_types[$i][1];// hardcoded dom support from array
				$browser_working = $a_browser_types[$i][2];// working name for browser
				$ua_type = $a_browser_types[$i][3];// sets whether bot or browser

				switch ( $browser_working )
				{
					// this is modified quite a bit, now will return proper netscape version number
					// check your implementation to make sure it works
					case 'ns':
						$b_safe_browser = false;
						$browser_number = $this->get_item_version( 'mozilla' );
						break;
					case 'moz':
						/*
						note: The 'rv' test is not absolute since the rv number is very different on
						different versions, for example Galean doesn't use the same rv version as Mozilla,
						neither do later Netscapes, like 7.x. For more on this, read the full mozilla
						numbering conventions here: http://www.mozilla.org/releases/cvstags.html
						*/
						// this will return alpha and beta version numbers, if present
						$moz_rv_full = $this->get_item_version( 'rv' );
						// this slices them back off for math comparisons
						$moz_rv = substr( $moz_rv_full, 0, 3 );

						// this is to pull out specific mozilla versions, firebird, netscape etc..
						$j_count = count( $a_moz_types );
						for ( $j = 0; $j < $j_count; $j++ )
						{
							if ( strstr( $browser_user_agent, $a_moz_types[$j] ) )
							{
								$moz_type = $a_moz_types[$j];
								$moz_number = $this->get_item_version( $moz_type );
								break;
							}
						}
						/*
						this is necesary to protect against false id'ed moz'es and new moz'es.
						this corrects for galeon, or any other moz browser without an rv number
						*/
						if ( !$moz_rv )
						{
						  $moz_rv = floatval( $moz_number );
							$moz_rv_full = $moz_number;
						}
						// this corrects the version name in case it went to the default 'rv' for the test
						if ( $moz_type == 'rv' )
						{
							$moz_type = 'mozilla';
						}

						//the moz version will be taken from the rv number, see notes above for rv problems
						$browser_number = $moz_rv;
						// gets the actual release date, necessary if you need to do functionality tests
						$this->get_set_count( 'set', 0 );
						$moz_release_date = $this->get_item_version( 'gecko/' );
						/*
						Test for mozilla 0.9.x / netscape 6.x
						test your javascript/CSS to see if it works in these mozilla releases, if it
						does, just default it to: $b_safe_browser = true;
						*/
						if ( ( $moz_release_date < 20020400 ) || ( $moz_rv < 1 ) )
						{
							$b_safe_browser = false;
						}
						break;
					case 'ie':
						/*
						note we're adding in the trident/ search to return only first instance in case
						of msie 8, and we're triggering the  break last condition in the test, as well
						as the test for a second search string, trident/
						*/
						$browser_number = $this->get_item_version( $browser_name, true, 'trident/' );
						// construct the proper real number if it's in compat mode and msie 8.0/9.0
						if ( strstr( $browser_number, '7.' ) && strstr( $browser_user_agent, 'trident/5' ) )
						{
							// note that 7.0 becomes 9 when adding 1, but if it's 7.1 it will be 9.1
							$true_ie_number = $browser_number + 2;
						}
						elseif ( strstr( $browser_number, '7.' ) && strstr( $browser_user_agent, 'trident/4' ) )
						{
							// note that 7.0 becomes 8 when adding 1, but if it's 7.1 it will be 8.1
							$true_ie_number = $browser_number + 1;
						}
						// the 9 series is finally standards compatible, html 5 etc, so worth a new id
						if ( $browser_number >= 9 )
						{
							$ie_version = 'ie9x';
						}
						// 7/8 were not yet quite to standards levels but getting there
						elseif ( $browser_number >= 7 )
						{
							$ie_version = 'ie7x';
						}
						// then test for IE 5x mac, that's the most problematic IE out there
						elseif ( strstr( $browser_user_agent, 'mac') )
						{
							$ie_version = 'ieMac';
						}
						// ie 5/6 are both very weak in standards compliance
						elseif ( $browser_number >= 5 )
						{
							$ie_version = 'ie5x';
						}
						elseif ( ( $browser_number > 3 ) && ( $browser_number < 5 ) )
						{
							$b_dom_browser = false;
							$ie_version = 'ie4';
							// this depends on what you're using the script for, make sure this fits your needs
							$b_safe_browser = true;
						}
						else
						{
							$ie_version = 'old';
							$b_dom_browser = false;
							$b_safe_browser = false;
						}
						break;
					case 'op':
						$browser_number = $this->get_item_version( $browser_name );
						// opera is leaving version at 9.80 (or xx) for 10.x - see this for explanation
						// http://dev.opera.com/articles/view/opera-ua-string-changes/
						if ( strstr( $browser_number, '9.' ) && strstr( $browser_user_agent, 'version/' ) )
						{
							$this->get_set_count( 'set', 0 );
							$browser_number = $this->get_item_version( 'version/' );
						}

						if ( $browser_number < 5 )// opera 4 wasn't very useable.
						{
							$b_safe_browser = false;
						}
						break;
					/*
					note: webkit returns always the webkit version number, not the specific user
					agent version, ie, webkit 583, not chrome 0.3
					*/
					case 'webkit':
						// note that this is the Webkit version number
						$browser_number = $this->get_item_version( $browser_name );
						// this is to pull out specific webkit versions, safari, google-chrome etc..
						$j_count = count( $a_webkit_types );
						for ( $j = 0; $j < $j_count; $j++ )
						{
							if ( strstr( $browser_user_agent, $a_webkit_types[$j] ) )
							{
								$webkit_type = $a_webkit_types[$j];
								/*
								and this is the webkit type version number, like: chrome 1.2
								if omni web, we want the count 2, not default 1
								*/
								if ( $webkit_type == 'omniweb' )
								{
									$this->get_set_count( 'set', 2 );
								}
								$webkit_type_number = $this->get_item_version( $webkit_type );
								// epiphany hack
								if ( $a_webkit_types[$j] == 'gtklauncher' )
								{
									$browser_name = 'epiphany';
								}
								else
								{
									$browser_name = $a_webkit_types[$j];
								}
								break;
							}
						}
						break;
					default:
						$browser_number = $this->get_item_version( $browser_name );
						break;
				} // del switch
				// the browser was id'ed
				$b_success = true;
				break;
			} // del if
    } // del for

		//assigns defaults if the browser was not found in the loop test
		if ( !$b_success )
		{
			/*
			this will return the first part of the browser string if the above id's failed
			usually the first part of the browser string has the navigator useragent name/version in it.
			This will usually correctly id the browser and the browser number if it didn't get
			caught by the above routine.
			If you want a '' to do a if browser == '' type test, just comment out all lines below
			except for the last line, and uncomment the last line. If you want undefined values,
			the browser_name is '', you can always test for that
			*/
			// delete this part if you want an unknown browser returned
			$browser_name = substr( $browser_user_agent, 0, strcspn( $browser_user_agent , '();') );
			// this extracts just the browser name from the string, if something usable was found
			if ( $browser_name && preg_match( '/[^0-9][a-z]*-*\ *[a-z]*\ *[a-z]*/', $browser_name, $a_unhandled_browser ) )
			{
				$browser_name = $a_unhandled_browser[0];

				if ( $browser_name == 'blackberry' )
				{
					$this->get_set_count( 'set', 0 );
				}
				$browser_number = $this->get_item_version( $browser_name );
			}
			else
			{
				$browser_name = 'NA';
				$browser_number = 'NA';
			}

			// then uncomment this part
			//$browser_name = '';//deletes the last array item in case the browser was not a match
		}
		// get os data, mac os x test requires browser/version information, this is a change from older scripts
		if ( $b_os_test )
		{
			$a_os_data = $this->get_os_data( $browser_working, $browser_number );
			$os_type = $a_os_data[0];// os name, abbreviated
			$os_number = $a_os_data[1];// os number or version if available
		}
		/*
		this ends the run through once if clause, set the boolean
		to true so the function won't retest everything
		*/
		$b_repeat = true;
		/*
		pulls out primary version number from more complex string, like 7.5a,
		use this for numeric version comparison
		*/
		if ( $browser_number && preg_match( '/[0-9]*\.*[0-9]*/', $browser_number, $a_browser_math_number ) )
		{
			$browser_math_number = $a_browser_math_number[0];
			//print_r($a_browser_math_number);
		}
		if ( $b_mobile_test )
		{
			$mobile_test = $this->check_is_mobile();
			if ( $mobile_test )
			{
				$a_mobile_data = $this->get_mobile_data();
				$ua_type = 'mobile';
			}
		}

  	/*
  	assemble these first so they can be included in full return data, using static variables
  	Note that there's no need to keep repacking these every time the script is called
  	*/
  	if ( !$a_moz_data )
  	{
  		$a_moz_data = [ $moz_type, $moz_number, $moz_rv, $moz_rv_full, $moz_release_date ];
  	}
  	if ( !$a_webkit_data )
  	{
  		$a_webkit_data = [ $webkit_type, $webkit_type_number, $browser_number ];
  	}

  	// then pack the primary data array
  	$a_full_assoc_data = [
  			'browser_working' => $browser_working,
  			'browser_number' => $browser_number,
  			'ie_version' => $ie_version,
  			'dom' => $b_dom_browser,
  			'safe' => $b_safe_browser,
  			'os' => $os_type,
  			'os_number' => $os_number,
  			'browser_name' => $browser_name,
  			'ua_type' => $ua_type,
  			'browser_math_number' => $browser_math_number,
  			'moz_data' => $a_moz_data,
  			'webkit_data' => $a_webkit_data,
  			'mobile_test' => $mobile_test,
  			'mobile_data' => $a_mobile_data,
  			'true_ie_number' => $true_ie_number
  		];
  	$this->setBrowserData($a_full_assoc_data);
  }

  // gets which os from the browser string
  public function get_os_data ( $pv_browser_name, $pv_version_number  )
  {
    $pv_browser_string = strtolower($this->getUa());
  	// initialize variables
  	$os_working_type = '';
  	$os_working_number = '';
  	/*
  	packs the os array. Use this order since some navigator user agents will put 'macintosh'
  	in the navigator user agent string which would make the nt test register true
  	*/
  	$a_mac = [ 'intel mac', 'ppc mac', 'mac68k' ];// this is not used currently
  	// same logic, check in order to catch the os's in order, last is always default item
  	$a_unix_types = [ 'dragonfly', 'freebsd', 'openbsd', 'netbsd', 'bsd', 'unixware', 'solaris', 'sunos', 'sun4', 'sun5', 'suni86', 'sun', 'irix5', 'irix6', 'irix', 'hpux9', 'hpux10', 'hpux11', 'hpux', 'hp-ux', 'aix1', 'aix2', 'aix3', 'aix4', 'aix5', 'aix', 'sco', 'unixware', 'mpras', 'reliant', 'dec', 'sinix', 'unix' ];
  	// only sometimes will you get a linux distro to id itself...
  	$a_linux_distros = [ 'ubuntu', 'kubuntu', 'xubuntu', 'mepis', 'xandros', 'linspire', 'winspire', 'jolicloud', 'sidux', 'kanotix', 'debian', 'opensuse', 'suse', 'fedora', 'redhat', 'slackware', 'slax', 'mandrake', 'mandriva', 'gentoo', 'sabayon', 'linux' ];
  	$a_linux_process = [ 'i386', 'i586', 'i686' ];// not use currently
  	// note, order of os very important in os array, you will get failed ids if changed
  	$a_os_types = [ 'android', 'blackberry', 'iphone', 'palmos', 'palmsource', 'symbian', 'beos', 'os2', 'amiga', 'webtv', 'mac', 'nt', 'win', $a_unix_types, $a_linux_distros ];

  	//os tester
  	$i_count = count( $a_os_types );
  	for ( $i = 0; $i < $i_count; $i++ )
  	{
  		// unpacks os array, assigns to variable $a_os_working
  		$os_working_data = $a_os_types[$i];
  		/*
  		assign os to global os variable, os flag true on success
  		!strstr($pv_browser_string, "linux" ) corrects a linux detection bug
  		*/
  		if ( !is_array( $os_working_data ) && strstr( $pv_browser_string, $os_working_data ) && !strstr( $pv_browser_string, "linux" ) )
  		{
  			$os_working_type = $os_working_data;

  			switch ( $os_working_type )
  			{
  				// most windows now uses: NT X.Y syntax
  				case 'nt':
  					if ( strstr( $pv_browser_string, 'nt 6.1' ) )// windows 7
  					{
  						$os_working_number = 6.1;
  					}
  					elseif ( strstr( $pv_browser_string, 'nt 6.0' ) )// windows vista/server 2008
  					{
  						$os_working_number = 6.0;
  					}
  					elseif ( strstr( $pv_browser_string, 'nt 5.2' ) )// windows server 2003
  					{
  						$os_working_number = 5.2;
  					}
  					elseif ( strstr( $pv_browser_string, 'nt 5.1' ) || strstr( $pv_browser_string, 'xp' ) )// windows xp
  					{
  						$os_working_number = 5.1;//
  					}
  					elseif ( strstr( $pv_browser_string, 'nt 5' ) || strstr( $pv_browser_string, '2000' ) )// windows 2000
  					{
  						$os_working_number = 5.0;
  					}
  					elseif ( strstr( $pv_browser_string, 'nt 4' ) )// nt 4
  					{
  						$os_working_number = 4;
  					}
  					elseif ( strstr( $pv_browser_string, 'nt 3' ) )// nt 4
  					{
  						$os_working_number = 3;
  					}
  					break;
  				case 'win':
  					if ( strstr( $pv_browser_string, 'vista' ) )// windows vista, for opera ID
  					{
  						$os_working_number = 6.0;
  						$os_working_type = 'nt';
  					}
  					elseif ( strstr( $pv_browser_string, 'xp' ) )// windows xp, for opera ID
  					{
  						$os_working_number = 5.1;
  						$os_working_type = 'nt';
  					}
  					elseif ( strstr( $pv_browser_string, '2003' ) )// windows server 2003, for opera ID
  					{
  						$os_working_number = 5.2;
  						$os_working_type = 'nt';
  					}
  					elseif ( strstr( $pv_browser_string, 'windows ce' ) )// windows CE
  					{
  						$os_working_number = 'ce';
  						$os_working_type = 'nt';
  					}
  					elseif ( strstr( $pv_browser_string, '95' ) )
  					{
  						$os_working_number = '95';
  					}
  					elseif ( ( strstr( $pv_browser_string, '9x 4.9' ) ) || ( strstr( $pv_browser_string, ' me' ) ) )
  					{
  						$os_working_number = 'me';
  					}
  					elseif ( strstr( $pv_browser_string, '98' ) )
  					{
  						$os_working_number = '98';
  					}
  					elseif ( strstr( $pv_browser_string, '2000' ) )// windows 2000, for opera ID
  					{
  						$os_working_number = 5.0;
  						$os_working_type = 'nt';
  					}
  					break;
  				case 'mac':
  					if ( strstr( $pv_browser_string, 'os x' ) )
  					{
  						// if it doesn't have a version number, it is os x;
  						if ( strstr( $pv_browser_string, 'os x ' ) )
  						{
  							// numbers are like: 10_2.4, others 10.2.4
  							$os_working_number = str_replace( '_', '.', $this->get_item_version( 'os x' ) );
  						}
  						else
  						{
  							$os_working_number = 10;
  						}
  					}
  					/*
  					this is a crude test for os x, since safari, camino, ie 5.2, & moz >= rv 1.3
  					are only made for os x
  					*/
  					elseif ( ( $pv_browser_name == 'saf' ) || ( $pv_browser_name == 'cam' ) ||
  						( ( $pv_browser_name == 'moz' ) && ( $pv_version_number >= 1.3 ) ) ||
  						( ( $pv_browser_name == 'ie' ) && ( $pv_version_number >= 5.2 ) ) )
  					{
  						$os_working_number = 10;
  					}
  					break;
  				case 'iphone':
  					$os_working_number = 10;
  					break;
  				default:
  					break;
  			}
  			break;
  		}
  		/*
  		check that it's an array, check it's the second to last item
  		in the main os array, the unix one that is
  		*/
  		elseif ( is_array( $os_working_data ) && ( $i == ( $i_count - 2 ) ) )
  		{
  			$j_count = count($os_working_data);
  			for ($j = 0; $j < $j_count; $j++)
  			{
  				if ( strstr( $pv_browser_string, $os_working_data[$j] ) )
  				{
  					$os_working_type = 'unix'; //if the os is in the unix array, it's unix, obviously...
  					$os_working_number = ( $os_working_data[$j] != 'unix' ) ? $os_working_data[$j] : '';// assign sub unix version from the unix array
  					break;
  				}
  			}
  		}
  		/*
  		check that it's an array, check it's the last item
  		in the main os array, the linux one that is
  		*/
  		elseif ( is_array( $os_working_data ) && ( $i == ( $i_count - 1 ) ) )
  		{
  			$j_count = count($os_working_data);
  			for ($j = 0; $j < $j_count; $j++)
  			{
  				if ( strstr( $pv_browser_string, $os_working_data[$j] ) )
  				{
  					$os_working_type = 'lin';
  					// assign linux distro from the linux array, there's a default
  					//search for 'lin', if it's that, set version to ''
  					$os_working_number = ( $os_working_data[$j] != 'linux' ) ? $os_working_data[$j] : '';
  					break;
  				}
  			}
  		}
  	}

  	// pack the os data array for return to main function
  	$a_os_data = [ $os_working_type, $os_working_number ];

  	return $a_os_data;
  }

  /*
  Function Info:
  function returns browser number, gecko rv number, or gecko release date
  function get_item_version( $browser_user_agent, $search_string, $substring_length )
  $pv_extra_search='' allows us to set an additional search/exit loop parameter, but we
  only want this running when needed
  */
  public function get_item_version( $pv_search_string, $pv_b_break_last='', $pv_extra_search='' )
  {
    $pv_browser_user_agent = strtolower($this->getUa());
  	// 12 is the longest that will be required, handles release dates: 20020323; 0.8.0+
  	$substring_length = 15;
  	$start_pos = 0; // set $start_pos to 0 for first iteration
  	//initialize browser number, will return '' if not found
  	$string_working_number = '';
  	/*
  	use the passed parameter for $pv_search_string
  	start the substring slice right after these moz search strings
  	there are some cases of double msie id's, first in string and then with then number
  	$start_pos = 0;
  	this test covers you for multiple occurrences of string, only with ie though
  	with for example google bot you want the first occurance returned, since that's where the
  	numbering happens
  	*/
  	for ( $i = 0; $i < 4; $i++ )
  	{
  		//start the search after the first string occurrence
  		if ( strpos( $pv_browser_user_agent, $pv_search_string, $start_pos ) !== false )
  		{
  			// update start position if position found
  			$start_pos = strpos( $pv_browser_user_agent, $pv_search_string, $start_pos ) + strlen( $pv_search_string );
  			/*
  			msie (and maybe other userAgents requires special handling because some apps inject
  			a second msie, usually at the beginning, custom modes allow breaking at first instance
  			if $pv_b_break_last $pv_extra_search conditions exist. Since we only want this test
  			to run if and only if we need it, it's triggered by caller passing these values.
  			*/
  			if ( !$pv_b_break_last || ( $pv_extra_search && strstr( $pv_browser_user_agent, $pv_extra_search ) ) )
  			{
  				break;
  			}
  		}
  		else
  		{
  			break;
  		}
  	}
  	/*
  	Handles things like extra omniweb/v456, gecko/, blackberry9700
  	also corrects for the omniweb 'v'
  	*/
  	$start_pos += $this->get_set_count( 'get' );
  	$string_working_number = substr( $pv_browser_user_agent, $start_pos, $substring_length );

  	// Find the space, ;, or parentheses that ends the number
  	$string_working_number = substr( $string_working_number, 0, strcspn($string_working_number, ' );/') );

  	//make sure the returned value is actually the id number and not a string
  	// otherwise return ''
  	// strcspn( $string_working_number, '0123456789.') == strlen( $string_working_number)
  	//	if ( preg_match("/\\d/", $string_working_number) == 0 )
   	if ( !is_numeric( substr( $string_working_number, 0, 1 ) ) )
  	{
  		$string_working_number = '';
  	}
  	//$string_working_number = strrpos( $pv_browser_user_agent, $pv_search_string );
  	return $string_working_number;
  }

  public function get_set_count( $pv_type, $pv_value='' )
  {
  	static $slice_increment;
  	$return_value = '';
  	switch ( $pv_type )
  	{
  		case 'get':
  			// set if unset, ie, first use. note that empty and isset are not good tests here
  			if ( is_null( $slice_increment ) )
  			{
  				$slice_increment = 1;
  			}
  			$return_value = $slice_increment;
  			$slice_increment = 1; // reset to default
  			return $return_value;
  			break;
  		case 'set':
  			$slice_increment = $pv_value;
  			break;
  	}
  }

  /*
  Special ID notes:
  Novarra-Vision is a Content Transformation Server (CTS)
  */
  public function check_is_mobile()
  {
    $pv_browser_user_agent = strtolower($this->getUa());

  	$mobile_working_test = '';
  	/*
  	these will search for basic mobile hints, this should catch most of them, first check
  	known hand held device os, then check device names, then mobile browser names
  	This list is almost the same but not exactly as the 4 arrays in function below
  	*/
  	$a_mobile_search = [
  	/*
  	Make sure to use only data here that always will be a mobile, so this list is not
  	identical to the list of get_mobile_data
  	*/
  	// os
  	'android', 'epoc', 'linux armv', 'palmos', 'palmsource', 'windows ce', 'windows phone os', 'symbianos', 'symbian os', 'symbian', 'webos',
  	// devices - ipod before iphone or fails
  	'benq', 'blackberry', 'danger hiptop', 'ddipocket', ' droid', 'ipad', 'ipod', 'iphone', 'kindle', 'lge-cx', 'lge-lx', 'lge-mx', 'lge vx', 'lge ', 'lge-', 'lg;lx', 'nintendo wii', 'nokia', 'palm', 'pdxgw', 'playstation', 'sagem', 'samsung', 'sec-sgh', 'sharp', 'sonyericsson', 'sprint', 'zune', 'j-phone', 'n410', 'mot 24', 'mot-', 'htc-', 'htc_', 'htc ', 'sec-', 'sie-m', 'sie-s', 'spv ', 'vodaphone', 'smartphone', 'armv', 'midp', 'mobilephone',
  	// browsers
  	'avantgo', 'blazer', 'elaine', 'eudoraweb', 'iemobile',  'minimo', 'mobile safari', 'mobileexplorer', 'opera mobi', 'opera mini', 'netfront', 'opwv', 'polaris', 'semc-browser', 'up.browser', 'webpro', 'wms pie', 'xiino',
  	// services - astel out of business
  	'astel',  'docomo',  'novarra-vision', 'portalmmm', 'reqwirelessweb', 'vodafone'
  	];

  	// then do basic mobile type search, this uses data from: get_mobile_data()
  	$j_count = count( $a_mobile_search );
  	for ($j = 0; $j < $j_count; $j++)
  	{
  		if ( strstr( $pv_browser_user_agent, $a_mobile_search[$j] ) )
  		{
  			$mobile_working_test = $a_mobile_search[$j];
  			break;
  		}
  	}

  	return $mobile_working_test;
  }

  /*
  thanks to this page: http://www.zytrax.com/tech/web/mobile_ids.html
  for data used here
  */
  public function get_mobile_data()
  {
    $pv_browser_user_agent = strtolower($this->getUa());
  	$mobile_browser = '';
  	$mobile_browser_number = '';
  	$mobile_device = '';
  	$mobile_device_number = '';
  	$mobile_os = ''; // will usually be null, sorry
  	$mobile_os_number = '';
  	$mobile_server = '';
  	$mobile_server_number = '';

  	// browsers, show it as a handheld, but is not the os
  	$a_mobile_browser = [ 'avantgo', 'blazer', 'elaine', 'eudoraweb', 'iemobile',  'minimo', 'mobile safari', 'mobileexplorer', 'opera mobi', 'opera mini', 'netfront', 'opwv', 'polaris', 'semc-browser', 'up.browser', 'webpro', 'wms pie', 'xiino' ];
  	/*
  	This goes from easiest to detect to hardest, so don't use this for output unless you
  	clean it up more is my advice.
  	Special Notes: do not include milestone in general mobile type test above, it's too generic
  	*/
  	$a_mobile_device = [ 'benq', 'blackberry', 'danger hiptop', 'ddipocket', ' droid', 'htc_dream', 'htc espresso', 'htc hero', 'htc halo', 'htc huangshan', 'htc legend', 'htc liberty', 'htc paradise', 'htc supersonic', 'htc tattoo', 'ipad', 'ipod', 'iphone', 'kindle', 'lge-cx', 'lge-lx', 'lge-mx', 'lge vx', 'lg;lx', 'nintendo wii', 'nokia', 'palm', 'pdxgw', 'playstation', 'sagem', 'samsung', 'sec-sgh', 'sharp', 'sonyericsson', 'sprint', 'zunehd', 'zune', 'j-phone', 'milestone', 'n410', 'mot 24', 'mot-', 'htc-', 'htc_',  'htc ', 'lge ', 'lge-', 'sec-', 'sie-m', 'sie-s', 'spv ', 'smartphone', 'armv', 'midp', 'mobilephone' ];
  	/*
  	note: linux alone can't be searched for, and almost all linux devices are armv types
  	ipad 'cpu os' is how the real os number is handled
  	*/
  	$a_mobile_os = [ 'android', 'epoc', 'cpu os', 'iphone os', 'palmos', 'palmsource', 'windows phone os', 'windows ce', 'symbianos', 'symbian os', 'symbian', 'webos', 'linux armv'  ];

  	// sometimes there is just no other id for the unit that the CTS type service/server
  	$a_mobile_server = [ 'astel', 'docomo', 'novarra-vision', 'portalmmm', 'reqwirelessweb', 'vodafone' ];

  	$k_count = count( $a_mobile_browser );
  	for ( $k = 0; $k < $k_count; $k++ )
  	{
  		if ( strstr( $pv_browser_user_agent, $a_mobile_browser[$k] ) )
  		{
  			$mobile_browser = $a_mobile_browser[$k];
  			// this may or may not work, highly unreliable because mobile ua strings are random
  			$mobile_browser_number = $this->get_item_version( $mobile_browser );
  			break;
  		}
  	}
  	$k_count = count( $a_mobile_device );
  	for ( $k = 0; $k < $k_count; $k++ )
  	{
  		if ( strstr( $pv_browser_user_agent, $a_mobile_device[$k] ) )
  		{
  			$mobile_device = trim ( $a_mobile_device[$k], '-_' ); // but not space trims yet
  			if ( $mobile_device == 'blackberry' )
  			{
  				$this->get_set_count( 'set', 0 );
  			}
  			$mobile_device_number = $this->get_item_version( $mobile_device );
  			$mobile_device = trim( $mobile_device ); // some of the id search strings have white space
  			break;
  		}
  	}
  	$k_count = count( $a_mobile_os );
  	for ( $k = 0; $k < $k_count; $k++ )
  	{
  		if ( strstr( $pv_browser_user_agent, $a_mobile_os[$k] ) )
  		{
  			$mobile_os = $a_mobile_os[$k];
  			// this may or may not work, highly unreliable
  			$mobile_os_number = str_replace( '_', '.', $this->get_item_version( $mobile_os ) );
  			break;
  		}
  	}
  	$k_count = count( $a_mobile_server );
  	for ( $k = 0; $k < $k_count; $k++ )
  	{
  		if ( strstr( $pv_browser_user_agent, $a_mobile_server[$k] ) )
  		{
  			$mobile_server = $a_mobile_server[$k];
  			// this may or may not work, highly unreliable
  			$mobile_server_number = $this->get_item_version( $mobile_server );
  			break;
  		}
  	}
  	// just for cases where we know it's a mobile device already
  	if ( !$mobile_os && ( $mobile_browser || $mobile_device || $mobile_server ) && strstr( $pv_browser_user_agent, 'linux' ) )
  	{
  		$mobile_os = 'linux';
  		$mobile_os_number = $this->get_item_version( 'linux' );
  	}

  	$a_mobile_data = [ $mobile_device, $mobile_browser, $mobile_browser_number, $mobile_os, $mobile_os_number, $mobile_server, $mobile_server_number, $mobile_device_number ];
  	return $a_mobile_data;
  }
}
