<?php
				// If uninstall not called from WordPress, then exit
				if ( ! defined( "WP_UNINSTALL_PLUGIN" ) ) {
					exit;
				}

				$count=0; 
				$libname = "default_library_puvox.php";
				foreach(get_plugins() as $key=>$value)
				{
					$file = WP_PLUGIN_DIR. "/".$key;
					if( file_exists($file) && stripos(file_get_contents($file), $libname) !== false)
					{
						$count++;
					}
				}
				if ($count==1)
				{
					$lib = WP_PLUGIN_DIR."/".$libname;
					if ( file_exists($lib))
						@unlink($lib);
				}
				