<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\Request;
use app\modules\admin\models\RequestSearch;
use app\modules\admin\models\Category;
use app\modules\admin\models\Status;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UploadForm;
use yii\web\UploadedFile;
use yii\filters\AccessControl;

/**
 * RequestController implements the CRUD actions for Request model.
 */
class RequestController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'] // Проверка роли администратора
                    ]
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new \Exception('У вас нет доступа к этой странице');
                }
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * Lists all Request models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Request model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Request model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Request();

        if ($model->load(Yii::$app->request->post())) {

            $model->imageFileBefore = UploadedFile::getInstance($model, 'imageFileBefore'); // Получаем url к изображению из формы
            $model->imageFileAfter = UploadedFile::getInstance($model, 'imageFileAfter');
            if ($model->upload()) {
                $model->save(false);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing Request model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->imageFileBefore = UploadedFile::getInstance($model, 'imageFileBefore'); // Получаем url к изображению из формы
            $model->imageFileAfter = UploadedFile::getInstance($model, 'imageFileAfter');

            if ($model->imageFileBefore && $model->imageFileBefore !== $model->img_before){ // Если файл был загружен и название не сходится со значением из бд - удаляем старое фото
                unlink(Yii::$app->basePath . '/web/' . $model->img_before);
            }
            if ($model->imageFileAfter && $model->imageFileAfter !== $model->img_after){
                unlink(Yii::$app->basePath . '/web/' . $model->img_after);
            }
            if ($model->upload()) {
                $model->save(false);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Request model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->img_before) // Если у запроса есть ссылки на изображения в бд - удаляем их из uploads
        {
            unlink(Yii::$app->basePath . '/web/' . $model->img_before);
        }
        if ($model->img_after)
        {
            unlink(Yii::$app->basePath . '/web/' . $model->img_after);
        }

        $model->delete(); // Удаление запроса

        return $this->redirect(['index']);
    }

    /**
     * Finds the Request model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Request the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */


    protected function findModel($id)
    {
        if (($model = Request::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }
}
