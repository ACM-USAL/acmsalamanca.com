<?php
	// $cada =  registers by page
	// $numpag = total number of registers 
	// $pagTotal = total pages, rounded up to the next highest. 
	$pagTotal = ceil($numpag/$cada);

	// Set $pagActual = current page from parameter 'pag' of URL
	// $pagAnterior = previous page of current
	// $pagSiguiente = next page of current
	if (!isset($_GET['pag'])) {
		$pagActual = 1;
		$pagi= 1;
	} else {
		$pagActual=$_GET['pag'];
		$pagi= @$_GET['pag'];
	}
	$pagAnterior = $pagActual - 1;
	$pagSiguiente = $pagActual + 1;

	// 
	echo '<div class="pag">';
		echo '<div class="texter">';
			echo i18n_r('pages_comments/Pag').': ';
		echo '</div>';

		$pgIntervalo = 2; // number of pages, before and after of current page
		$pgMaximo = ($pgIntervalo*2)+1; // Maximum number of pages in pagination
		$pg = (($pagActual-$pgIntervalo)<=0) ? '2' : (($pagActual-$pgIntervalo)>($pagTotal-$pgMaximo) ? ($pagTotal-$pgMaximo) : ($pagActual-$pgIntervalo));
		$i = 0;

		//Previous page
		if ($pagActual > 1) {
			echo '<a class="prev" href="'.$idpret.'&amp;pag='.$pagAnterior.'#pcom" title="'.i18n_r('pages_comments/prevp').'"> '.i18n_r('pages_comments/Prev').' </a>';
		}

		//First page = 1
		$activ = ($pagActual == 1) ? 'class="activ"' : '';
		echo '<a '.$activ.' href="'.$idpret.'&amp;pag=1#pcom" title="'.i18n_r('pages_comments/pc_firstpage').'">1</a>';

		//separation only if first interval is out
		if ($pagAnterior > 3 && ($pagTotal) > 7){ echo '<span class="point">'.i18n_r('pages_comments/pc_separate').'</span>'; }

		//List of pages 
		while ($i<$pgMaximo) {
			$activ = ($pg == $pagActual) ? 'class="activ"' : '';
			if ($pg>1 and $pg<$pagTotal) {
				echo '<a '.$activ.' href="'.$idpret.'&amp;pag='.$pg.'#pcom">'.$pg.'</a>';
				$i++;
			}
			if ($pg > $pagTotal) {$i = $pgMaximo;} 
			$pg++;
		}

		//Separation only if last interval is out
		if (($pagTotal - $pagSiguiente) > 2 && ($pagTotal) > 7){ 
			echo '<span class="point">'.i18n_r('pages_comments/pc_separate').'</span>'; 
		}

		//Last page
		if ($pagTotal > 1){
			$activ = ($pagActual == $pagTotal) ? 'class="activ"' : '';
			echo '<a '.$activ.' href="'.$idpret.'&amp;pag='.$pagTotal.'#pcom" title="'.i18n_r('pages_comments/pc_lastpage').'">'.$pagTotal.'</a>';
		}

		//Next page
		if ($pagActual < $pagTotal) {
			echo '<a class="next" href="'.$idpret.'&amp;pag='.$pagSiguiente.'#pcom" title="'.i18n_r('pages_comments/nextp').'"> '.i18n_r('pages_comments/Next').' </a>';
		}

	echo '</div>';
?>
