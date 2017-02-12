<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question".
 *
 * @property integer $id
 * @property integer $id_cat_root
 * @property integer $id_sub1
 * @property integer $id_sub2
 * @property integer $id_sub3
 * @property string $content_question
 * @property string $answer_1
 * @property string $answer_2
 * @property string $answer_3
 * @property string $answer_4
 * @property string $correct_answer
 * @property integer $create_user
 * @property integer $upd_user
 * @property string $create_date
 * @property string $update_date
 */
class Question extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_cat_root', 'content_question'], 'required'],
            [['id_cat_root', 'id_sub1', 'id_sub2', 'id_sub3', 'create_user', 'upd_user'], 'integer'],
            [['content_question', 'answer_1', 'answer_2', 'answer_3', 'answer_4'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['correct_answer'], 'string', 'max' => 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_cat_root' => 'Id Cat Root',
            'id_sub1' => 'Id Sub1',
            'id_sub2' => 'Id Sub2',
            'id_sub3' => 'Id Sub3',
            'content_question' => 'Content Question',
            'answer_1' => 'Answer 1',
            'answer_2' => 'Answer 2',
            'answer_3' => 'Answer 3',
            'answer_4' => 'Answer 4',
            'correct_answer' => 'Correct Answer',
            'create_user' => 'Create User',
            'upd_user' => 'Upd User',
            'create_date' => 'Create Date',
            'update_date' => 'Update Date',
        ];
    }
}
