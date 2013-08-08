<?php

/* @var $this TvShowController */
$dataProvider = $this->getEpisodeDataProvider($tvshowId, $season);

?>

<div class="season-download">
	<?php echo TbHtml::linkButton('Watch the whole season', array(
		'color'=>TbHtml::BUTTON_COLOR_SUCCESS,
		'size'=>TbHtml::BUTTON_SIZE_LARGE,
		'url'=>$this->createUrl('tvShow/getSeasonPlaylist', 
				array('tvshowId'=>$tvshowId, 'season'=>$season)),
		'class'=>'fontastic-icon-play',
	)); ?> or choose individual episodes from the list below
</div>

<?php $this->widget('EpisodeList', array(
	'type'=>TbHtml::GRID_TYPE_STRIPED,
	'dataProvider'=>$dataProvider,
	'template'=>'{items}',
	'columns'=>array(
		array(
			'type'=>'raw',
			'header'=>'Episode',
			'value'=>function($data) {
				// $this not available in PHP < 5.4
				Yii::app()->controller->renderPartial('_getEpisode', array('episode'=>$data));
			}
		),
		array(
			'type'=>'raw',
			'header'=>'',
			'value'=>function($data) {
				$thumbnail = new ThumbnailVideo($data->thumbnail, Thumbnail::SIZE_SMALL);
				return Thumbnail::lazyImage($thumbnail, array('class'=>'item-thumbnail episode-thumbnail'));
			}
		),
		array(
			'header'=>'Title',
			'name'=>'title',
		),
		array(
			'type'=>'raw',
			'header'=>'Plot',
			'value'=>function($data) {
				// $this not available in PHP < 5.4
				Yii::app()->controller->renderPartial('_plotStreamDetails', array('episode'=>$data));
			}
		),
		array(
			'header'=>'Runtime',
			'type'=>'html',
			'value'=>function($data) {
				Yii::app()->controller->renderPartial('//videoLibrary/_runtime', 
						array('runtime'=>$data->runtime));
			}
		),
	),
));