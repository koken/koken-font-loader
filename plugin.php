<?php

class KokenFontLoader extends KokenPlugin {

	function __construct()
	{
		$this->require_setup = true;
		$this->register_hook('before_closing_head', 'render');
	}

	function render()
	{

		$output = (object) array();

		if (!empty($this->data->typekit)) {
			$output->typekit = (object) array('id' => $this->data->typekit);
		}

		if (!empty($this->data->google)) {
			$fonts = explode(',', $this->data->google);
			$families = array();
			$weights = false;
			foreach($fonts as $f) {
				if (strpos($f, '(') !== FALSE) {
					$weights = true;
					$parts = explode('(', $f);
					$family = trim($parts[0]) . ':400,' . trim($parts[1]);
					if (strpos($f, ')') !== FALSE) {
						$family = trim(str_replace(')', '', $family));
						$weights = false;
					}
				}
				if ($weights) {
					if (strpos($family, ':400') !== FALSE && !strpos($f, '(') && !strpos($f, ')')) {
						$family .= ',' . trim($f);
					}
					if (strpos($f, ')') !== FALSE) {
						if (!strpos($family, ':')) {
							$family .= ':';
						} else {
							$family .= ',';
						}
						$family .= str_replace(')', '', trim($f));
						$weights = false;
					}
				}
				if (!$weights) {
					array_push($families, (isset($family)) ? trim($family) : trim($f));
					unset($family);
				}
			}
			$output->google = (object) array('families' => $families);
		}

		if (!empty($this->data->fontdeck)) {
			$output->fontdeck = (object) array('id' => $this->data->fontdeck);
		}

		if (!empty($this->data->fontscom)) {
			$output->monotype = (object) array('projectId' => $this->data->fontscom);
		}

		$output = json_encode($output);
		$fallback = 'document.write(\'<script src="'.$this->get_path().'/webfont.js">\x3C/script>\')';

		echo <<<OUT
<script src="http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js"></script>
<script>window.WebFont || $fallback</script>
<script type="text/javascript">WebFont.load($output);</script>
OUT;

	}
}