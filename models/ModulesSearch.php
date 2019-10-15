<?php

namespace wdmg\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use wdmg\admin\models\Modules;

/**
 * ModulesSearch represents the model behind the search form of `wdmg\admin\models\Modules`.
 */
class ModulesSearch extends Modules
{


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['module', 'name', 'description', 'class', 'bootstrap', 'version', 'status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Modules::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'module', $this->module])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['like', 'bootstrap', $this->bootstrap])
            ->andFilterWhere(['like', 'version', $this->version]);

        if($this->status !== "*")
            $query->andFilterWhere(['like', 'status', $this->status]);

        $query->orderBy(['priority' => SORT_ASC]);

        return $dataProvider;
    }

}
