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
			$families = array();
			$xtras = '';
			$parts = explode(',',$this->data->google);
			foreach($parts as $family) {
				if (isset($this->data->gstyles)) {
					$xtras .= $this->data->gstyles . ',';
				}
				if (isset($this->data->gweights)) {
					$xtras .= $this->data->gweights . ',';
				}
				if (isset($this->data->gsubsets)) {
					$xtras .= $this->data->gsubsets . ',';
				}
				$xtras = rtrim($xtras, ',');
				if ($xtras !== '') {
					$family .= ':' . $xtras;
				}
				array_push($families,$family);
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