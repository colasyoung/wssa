<?php

/**
 * This is the model class for table "equipment_category".
 *
 * The followings are the available columns in table 'equipment_category':
 * @property string $id
 * @property integer $user_id
 * @property string $name
 * @property string $name_zh
 * @property string $sequence
 * @property integer $status
 */
class EquipmentCategory extends ActiveRecord {
	const STATUS_HIDE = 0;
	const STATUS_SHOW = 1;
	const STATUS_DELETE = 2;

	public static function getAllStatus() {
		return array(
			self::STATUS_HIDE=>'隐藏',
			self::STATUS_SHOW=>'发布',
			// self::STATUS_DELETE=>'删除',
		);
	}

	public static function getCategories() {
		$categories = self::model()->findAll(array(
			'order'=>'sequence DESC',
		));
		return CHtml::listData($categories, 'id', 'name_zh');
	}

	public static function getCategoryMenu() {
		$categories = self::model()->findAllByAttributes(array(
			'status'=>self::STATUS_SHOW,
		), array(
			'order'=>'sequence DESC',
		));
		$menu = [
			[
				'label'=>Yii::t('Equipment', 'All'),
				'url'=>['/equipment/index'],
				'active'=>!isset($_GET['category_id']),
			],
		];
		foreach ($categories as $category) {
			$menu[] = array(
				'label'=>$category->getAttributeValue('name'),
				'url'=>array('/equipment/index', 'category_id'=>$category->id),
			);
		}
		return $menu;
	}

	public function getStatusText() {
		$status = self::getAllStatus();
		return isset($status[$this->status]) ? $status[$this->status] : $this->status;
	}

	public function getOperationButton() {
		$buttons = array();
		$buttons[] = CHtml::link('编辑',  array('/board/equipment/editCategory',  'id'=>$this->id), array('class'=>'btn btn-xs btn-blue btn-square'));
		if (Yii::app()->user->checkPermission('faq_admin')) {
			switch ($this->status) {
				case self::STATUS_HIDE:
					$buttons[] = CHtml::link('发布',  array('/board/equipment/showCategory',  'id'=>$this->id), array('class'=>'btn btn-xs btn-green btn-square'));
					break;
				case self::STATUS_SHOW:
					$buttons[] = CHtml::link('隐藏',  array('/board/equipment/hideCategory',  'id'=>$this->id), array('class'=>'btn btn-xs btn-red btn-square'));
					break;
			}
		}
		return implode(' ',  $buttons);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'equipment_category';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, name, name_zh', 'required'),
			array('user_id, sequence, status', 'numerical', 'integerOnly'=>true),
			array('name, name_zh', 'length', 'max'=>128),
			array('sequence', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, name, name_zh, sequence, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user'=>array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'user_id' => '添加人',
			'name' => '英文名',
			'name_zh' => '中文名',
			'sequence' => '排序',
			'status' => '状态',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search() {
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('name_zh',$this->name_zh,true);
		$criteria->compare('sequence',$this->sequence,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'sequence DESC, id DESC',
			),
			'pagination'=>array(
				'pageVar'=>'page',
			),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EquipmentCategory the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}
