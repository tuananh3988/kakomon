<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "like".
 *
 * @property integer $like_id
 * @property integer $comment_id
 * @property integer $member_id
 * @property integer $delete_flag
 * @property integer $like_flag
 * @property string $created_date
 * @property string $updated_date
 */
class Like extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'like';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'member_id'], 'required'],
            [['comment_id', 'member_id', 'delete_flag', 'like_flag'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'like_id' => 'Like ID',
            'comment_id' => 'Comment ID',
            'member_id' => 'Member ID',
            'delete_flag' => 'Delete Flag',
            'like_flag' => 'Like Flag',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
}
