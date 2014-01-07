<?php

if ( class_exists( 'ZipArchive' ) ) {
	class Zipper extends ZipArchive {
		protected $archive  = null;

		protected $root_dir = null;

		public function __construct( $file ) {
			if ( is_resource( $file ) ) {
				$meta = stream_get_meta_data( $file );
				$this->archive = $meta['uri'];
			} else {
				$this->archive = $file;
			}

			// Open Archive File
			if ( !( $this->open( $this->archive ) === true ) ) {
				throw new RuntimeException( 'Archive file cound not be created.' );
			}
		}

		public function addDir( $path, $parent_dir = null, $include = array() ) {
			// Use Recursive functions
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $path , RecursiveDirectoryIterator::SKIP_DOTS ),
				RecursiveIteratorIterator::SELF_FIRST
			);

			// Prepare File Filter Pattern
			$file_pattern = null;
			if ( is_array( $include ) ) {
				$filters = array();
				foreach ( $include as $file ) {
					$filters[] = str_replace( '\.\*', '.*' , preg_quote( $file, '/' ) );
				}

				$file_pattern = implode( '|', $filters );
			}

			foreach ( $iterator as $item ) {
				// Validate file pattern
				if ( $file_pattern ) {
					if ( ! preg_match( '/^(' . $file_pattern . ')$/', $iterator->getSubPathName() ) ) {
						continue;
					}
				}

				// Add to archive
				if ( $item->isDir() ) {
					$this->addEmptyDir( $parent_dir . DIRECTORY_SEPARATOR . $iterator->getSubPathName() );
				} else {
					$this->addFile( $item->getPathname(), $parent_dir . DIRECTORY_SEPARATOR . $iterator->getSubPathName() );
				}
			}
		}

		public function getArchive() {
			return $this->archive;
		}
	}
}
