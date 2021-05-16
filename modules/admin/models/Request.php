<?php

namespace app\modules\admin\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "request".
 *
 * @property int $id
 * @property string $name Название запроса
 * @property int $categoryID Категория
 * @property int $statusID Статус
 * @property string|null $reject_msg Причина отказа
 * @property string $img_before Изображение "До"
 * @property string|null $img_after Изображение "После"
 * @property int $created_by Автор
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Category $category
 * @property Status $status
 */
class Request extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request';
    }
    public $imageFileBefore;
    public $imageFileAfter;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'categoryID'], 'required'],
            [['categoryID', 'statusID', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'reject_msg', 'img_before', 'img_after'], 'string', 'max' => 255],
            [['imageFileBefore'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, bmp, jpeg', 'maxSize' => 10 * 1024 * 1024],
            [['imageFileAfter'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, bmp, jpeg', 'maxSize' => 10 * 1024 * 1024],
            [['categoryID'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['categoryID' => 'id']],
            [['statusID'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['statusID' => 'id']],
        ];
    }
    public function behaviors()
    {
        return [
            [ // Перед сохранением строки - ввод timestamp'а в необходимые поля
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()')
            ],
            [ // Перед сохранением строки - ввод ID пользователя в необходимые поля
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by'
            ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название заявки',
            'categoryID' => 'Категория',
            'statusID' => 'Статус',
            'reject_msg' => 'Причина отказа',
            'img_before' => 'Изображение',
            'img_after' => 'Изображение результата работ',
            'created_by' => 'Автор',
            'updated_by' => 'Обновлено',
            'created_at' => 'Время создания',
            'updated_at' => 'Время обновления',
            'imageFileBefore' => 'Изображение',
            'imageFileAfter' => 'Изображение результата работ'
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'categoryID']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'statusID']);
    }

    public function upload()
    {
        if ($this->validate()) {
            if ($this->imageFileBefore) // Если загружен файл - сохранение изображения в web/uploads и запись пути в переменную модели
            {
                $pathBefore = 'uploads/' . $this->imageFileBefore->baseName . '-' . time() . '.' . $this->imageFileBefore->extension; // Добавляем timestamp для уникальности файла
                $this->imageFileBefore->saveAs($pathBefore);
                $this->img_before = $pathBefore;
            }
            if ($this->imageFileAfter)
            {
                $pathAfter = 'uploads/' . $this->imageFileAfter->baseName . '-' . time() . '.' . $this->imageFileAfter->extension;
                $this->imageFileAfter->saveAs($pathAfter);
                $this->img_after = $pathAfter;
            }
            return true;
        } else {
            return false;
        }
    }
}
