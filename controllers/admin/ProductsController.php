<?php
namespace app\system\modules\shop\controllers\admin;

use Yii;
use panix\engine\controllers\AdminController;
use yii\helpers\Url;
use app\system\modules\shop\models\ShopProduct;
use app\system\modules\shop\models\ShopProductSearch;

class ProductsController extends AdminController {

  /*  public function actions() {
        return array(
            'switch' => array(
                'class' => 'ext.adminList.actions.SwitchAction',
            ),
            'order' => array(
                'class' => 'ext.adminList.actions.SortingAction',
            ),
        );
    }*/

    /*
      public function cover($id) {
      $photo = ShopProductImage::model()->findByPk($_POST['cover']);
      $photo->setCover();
      } */

    /**
     * Display list of products
     */
    public function actionIndex() {

        //$this->topButtons = Html::link(Yii::t('core', 'CREATE', 0), $this->createUrl('create'), array('title' => Yii::t('admin', 'Create', 1), 'class' => 'buttonS bGreen'));
        $this->pageName = Yii::t('shop/default', 'PRODUCTS');

       // $this->breadcrumbs = array(
      //      Yii::t('shop/default', 'MODNAME') => array('/admin/shop'),
       //     $this->pageName
      //  );

       // Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl . '/admin/products.index.js', CClientScript::POS_END);

        if (Yii::$app->settings->get('shop', 'create_btn_action')) {
            $url = Url::toRoute('create');
            //$url = $this->createUrl('create', array('ShopProduct[type_id]' => Yii::$app->settings->get('shop', 'create_btn_action'), 'ShopProduct[use_configurations]' => 0));
        } else {
            $url=Url::toRoute('create');
            //$url = $this->createUrl('create');
        }

        $this->buttons = array(array(
                'label' => Yii::t('shop/default', 'CREATE_PRODUCT'),
                'url' => $url,
                'options' => array('class' => 'btn btn-success')
                ));

        $model = new ShopProductSearch();

       // if (!empty($_GET['ShopProduct']))
       //     $model->attributes = $_GET['ShopProduct'];

        // Pass additional params to search method.
      //  $params = array(
       //     'category' => Yii::$app->request->('category', null)
       // );

        $dataProvider = $model->search([]); //$params
      //  $model->unsetAttributes();
        return $this->render('index', array(
            'model' => $model,
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Create/update product
     * @param bool $new
     * @throws CHttpException
     */
    public function actionUpdate($new = false) {
        $this->topButtons = false;
        $config = Yii::app()->settings->get('shop');
        if ($new === true) {
            $model = new ShopProduct();
        } else {
           $model = ShopProduct::model()->language(Yii::app()->language->active)->findByPk($_GET['id']);
        }
        if (!$model)
            throw new CHttpException(404, Yii::t('shop/default', 'NO_FOUND_PRODUCT'));

        $oldImage = $model->image;

        if (!$model->isNewRecord) {
            $this->buttons = array(array(
                    'label' => Yii::t('shop/default', 'VIEW_PRODUCT'),
                    'url' => $model->getAbsoluteUrl(),
                    'htmlOptions' => array('class' => 'buttonS bBlue', 'target' => '_blank'),
                    ));
        }
        // Apply use_configurations, configurable_attributes, type_id
        if (isset($_GET['ShopProduct']))
            $model->attributes = $_GET['ShopProduct'];




        $title = ($model->isNewRecord) ? Yii::t('ShopModule.admin', 'CREATE_PRODUCT') :
                Yii::t('ShopModule.admin', 'UPDATE_PRODUCT');

        if ($model->type)
            $title .= ' "' . Html::encode($model->type->name) . '"';

        $this->pageName = $title;



        $this->breadcrumbs = array(
            Yii::t('ShopModule.default', 'MODULE_NAME') => array('/admin/shop'),
            Yii::t('ShopModule.admin', 'PRODUCTS') => $this->createUrl('index'),
            $this->pageName
        );


        // On create new product first display "Choose type" form first.
        if ($model->isNewRecord && isset($_GET['ShopProduct']['type_id'])) {
            if (ShopProductType::model()->countByAttributes(array('id' => $model->type_id)) === '0')
                throw new CHttpException(404, Yii::t('ShopModule.admin', 'ERR_PRODUCT_TYPE'));
        }

        // Set main category id to have categories drop-down selected value
        if ($model->mainCategory)
            $model->main_category_id = $model->mainCategory->id;

        // Or set selected category from type pre-set.
        if ($model->type && !Yii::app()->request->isPostRequest && $model->isNewRecord)
            $model->main_category_id = $model->type->main_category;

        // Set configurable attributes on new record
        if ($model->isNewRecord) {
            if ($model->use_configurations && isset($_GET['ShopProduct']['configurable_attributes']))
                $model->configurable_attributes = $_GET['ShopProduct']['configurable_attributes'];
        }

        $form = new TabForm($model->getForm(), $model);
        //  $form->positionTabs = 'vertical';
        // Set additional tabs

        $form->additionalTabs[$model->t('TAB_CAT')] = array(
            'content' => $this->renderPartial('_tree', array('model' => $model), true)
            );
                $form->additionalTabs[$model->t('TAB_IMG')] = array('content' => $this->renderPartial('_images', array('model' => $model, 'uploadModel' => $uploadModel), true));
        $form->additionalTabs[$model->t('TAB_ATTR')] = array('content' => $this->renderPartial('_attributes', array('model' => $model), true));
     /*  if (Yii::app()->getModule('shop')->relatedProducts)
            $form->additionalTabs[$model->t('TAB_REL')] = array('content' => $this->renderPartial('_relatedProducts', array('exclude' => $model->id, 'product' => $model), true));
*/
        //if (Yii::app()->getModule('shop')->variations)
       /* $form->additionalTabs[Yii::t('ShopModule.admin', 'UPDATE_PRODUCT_TAB_VARIANTS')] = array(
            'content' => $this->renderPartial('_variations', array('model' => $model), true)
        );*/
        // if($this->isInstallModule('comments')){
        //      $form->additionalTabs['icon-comment'] = array(
        //         'content' => $this->renderPartial('_comments', array('model' => $model), true)
        //         );
        // }



        /*    $form->additionalTabs = array(
          'icon-folder-open' => array(
          'content' => $this->renderPartial('_tree', array('model' => $model), true)
          ),
          'icon-copy-3' => array(
          'content' => $this->renderPartial('_relatedProducts', array('exclude' => $model->id, 'product' => $model), true)
          ),
          'icon-images' => array(
          'content' => $this->renderPartial('_images', array('model' => $model, 'uploadModel' => $uploadModel), true)
          ),
          'icon-paragraph-justify' => array(
          'content' => $this->renderPartial('_attributes', array('model' => $model), true),
          'visible'=>false
          ),
          Yii::t('ShopModule.admin', 'Варианты') => array(
          'content' => $this->renderPartial('_variations', array('model' => $model), true)
          ),
          'icon-comment' => array(
          'content' => $this->renderPartial('_comments', array('model' => $model), true)
          ),
          ); */

        if ($model->use_configurations)
            $form->additionalTabs[Yii::t('ShopModule.admin', 'UPDATE_PRODUCT_TAB_CONF')] = array(
                'content' => $this->renderPartial('_configurations', array('product' => $model), true)
            );
        if (isset($_GET['ShopProduct']['main_category_id']))
            $model->main_category_id = $_GET['ShopProduct']['main_category_id'];
        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['ShopProduct'];

            // Handle related products
            $model->setRelatedProducts(Yii::app()->getRequest()->getPost('RelatedProductId', array()));
            if ($config['auto_gen_url']) {
                $model->name = ShopCategory::model()->findByPk($model->main_category_id)->name . ' ' . ShopManufacturer::model()->findByPk($model->manufacturer_id)->name . ' ' . $model->sku;
                $model->seo_alias = CMS::translit($model->name);
                // die($model->name.$model->seo_alias);
            }
            /* if($model->currency_id){
              $currency = ShopCurrency::model()->findByPk($model->currency_id);
              $convertPrice = $model->price*$currency->rate/$currency->rate_old;
              $model->price=$convertPrice;
              } */
            if ($model->validate() && $this->validateAttributes($model)) {
                $model->saveImage('image', 'webroot.uploads.product', $oldImage);
                $model->save();
                // Process categories
                $mainCategoryId = 1;
                if (isset($_POST['ShopProduct']['main_category_id']))
                    $mainCategoryId = $_POST['ShopProduct']['main_category_id'];

                $model->setCategories(Yii::app()->request->getPost('categories', array()), $mainCategoryId);

                // Process attributes
                $this->processAttributes($model);

                // Process variants
                $this->processVariants($model);

                // Process configurations
                $this->processConfigurations($model);

                // Handle images
                $this->handleUploadedImages($model);

                // Set main image
                $this->updateMainImage($model);

                // Update image titles
                $this->updateImageTitles();



                if ($model->save(false, false)) {
                    if ($config['save_fields_on_create'])
                        $this->saveFields();
                }

                if ($new === true) {
                    if ($config['save_fields_on_create']) {
                        $getArray = array('create');
                        $getArray['ShopProduct[type_id]'] = $_GET['ShopProduct']['type_id'];
                        $getArray['ShopProduct[use_configurations]'] = $_GET['ShopProduct']['use_configurations'];
                        foreach ($_POST['ShopProduct'] as $key => $params) {
                            $getArray['ShopProduct[' . $key . ']'] = $params;
                        }
                        $this->redirect($getArray);
                    } else {
                        $this->redirect(array('index'));
                    }
                } else {

                    if ($config['product_button_update'] == 1) {
                        $this->redirect(array('index'));
                    } else {
                        $this->redirect($this->createUrl('update', array('id' => $model->id)));
                    }
                }
            } else {
                $this->setFlashMessage(Yii::t('ShopModule.admin', 'ERR_PRODUCT_TYPE'));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'form' => $form,
        ));
    }

    private function saveFields() {
        $get = $_GET['ShopProduct'];
        $post = $_POST['ShopProduct'];
        unset($_POST['submit'], $_POST['yform_ShopProduct'], $_POST['token']);
        $urlParams = array(
            'create',
            'ShopProduct[use_configurations]' => $get['use_configurations'],
            'ShopProduct[type_id]' => $get['type_id'],
            'ShopProduct[price]' => $post['price'],
            'ShopProduct[name]' => $post['name'],
            'ShopProduct[seo_alias]' => $post['seo_alias'],
            'ShopProduct[switch]' => $post['switch'],
            'ShopProduct[main_category_id]' => $post['main_category_id'],
            'ShopProduct[manufacturer_id]' => $post['manufacturer_id'],
            'ShopProduct[supplier_id]' => $post['supplier_id'],
        );

        $this->redirect($urlParams);
    }

    /**
     * Save model attributes
     * @param ShopProduct $model
     * @return boolean
     */
    protected function processAttributes(ShopProduct $model) {
        $attributes = new CMap(Yii::app()->request->getPost('ShopAttribute', array()));
        if (empty($attributes))
            return false;

        $deleteModel = ShopProduct::model()->findByPk($model->id);
        $deleteModel->deleteEavAttributes(array(), true);

        // Delete empty values
        foreach ($attributes as $key => $val) {
            if (is_string($val) && $val === '')
                $attributes->remove($key);
        }

        return $model->setEavAttributes($attributes->toArray(), true);
    }

    /**
     * Save product variants
     * @param ShopProduct $model
     */
    protected function processVariants(ShopProduct $model) {
        $dontDelete = array();

        if (!empty($_POST['variants'])) {
            foreach ($_POST['variants'] as $attribute_id => $values) {
                $i = 0;
                foreach ($values['option_id'] as $option_id) {
                    // Try to load variant from DB
                    $variant = ShopProductVariant::model()->findByAttributes(array(
                        'product_id' => $model->id,
                        'attribute_id' => $attribute_id,
                        'option_id' => $option_id
                            ));
                    // If not - create new.
                    if (!$variant)
                        $variant = new ShopProductVariant();

                    $variant->setAttributes(array(
                        'attribute_id' => $attribute_id,
                        'option_id' => $option_id,
                        'product_id' => $model->id,
                        'price' => $values['price'][$i],
                        'price_type' => $values['price_type'][$i],
                        'sku' => $values['sku'][$i],
                            ), false);

                    $variant->save(false, false, false);
                    array_push($dontDelete, $variant->id);
                    $i++;
                }
            }
        }

        if (!empty($dontDelete)) {
            $cr = new CDbCriteria;
            $cr->addNotInCondition('id', $dontDelete);
            $cr->addCondition('product_id=' . $model->id);
            ShopProductVariant::model()->deleteAll($cr);
        }else
            ShopProductVariant::model()->deleteAllByAttributes(array('product_id' => $model->id));
    }

    /**
     * Save product configurations
     * @param ShopProduct $model
     * @return mixed
     */
    protected function processConfigurations(ShopProduct $model) {
        $productPks = Yii::app()->request->getPost('ConfigurationsProductGrid_c0', array());

        // Clear relations
        Yii::app()->db->createCommand()->delete('{{shop_product_configurations}}', 'product_id=:id', array(':id' => $model->id));

        if (!sizeof($productPks))
            return;

        foreach ($productPks as $pk) {
            Yii::app()->db->createCommand()->insert('{{shop_product_configurations}}', array(
                'product_id' => $model->id,
                'configurable_id' => $pk
            ));
        }
    }

    /**
     * Create gridview for "Related Products" tab
     * @param int $exclude Product id to exclude from list
     */
    public function actionApplyProductsFilter($exclude = 0) {
        $model = new ShopProduct('search');
        $model->exclude = $exclude;

        if (!empty($_GET['RelatedProducts']))
            $model->attributes = $_GET['RelatedProducts'];

        $this->renderPartial('_relatedProducts', array(
            'model' => $model,
            'exclude' => $exclude,
        ));
    }

    /**
     * Render configurations tab gridview.
     */
    public function actionApplyConfigurationsFilter() {
        $product = ShopProduct::model()->findByPk($_GET['product_id']);

        // On create new product
        if (!$product) {
            $product = new ShopProduct();
            $product->configurable_attributes = $_GET['configurable_attributes'];
        }

        $this->renderPartial('_configurations', array(
            'product' => $product,
            'clearConfigurations' => true // Show all products
        ));
    }

    /**
     * Render comments tab
     */
    public function actionApplyCommentsFilter() {
        $this->renderPartial('_comments', array(
            'model' => ShopProduct::model()->findByPk($_GET['id'])
        ));
    }

    /**
     * @throws CHttpException
     */
    public function actionRenderVariantTable() {
        $attribute = ShopAttribute::model()
                ->findByPk($_GET['attr_id']);

        if (!$attribute)
            throw new CHttpException(404, Yii::t('ShopModule.admin', 'ERR_LOAD_ATTR'));

        $this->renderPartial('variants/_table', array(
            'attribute' => $attribute
        ));
    }

    /**
     * Load attributes relative to type and available for product configurations.
     * Used on creating new product.
     */
    public function actionLoadConfigurableOptions() {
        // For configurations that  are available only dropdown and radio lists.
        $cr = new CDbCriteria;
        $cr->addInCondition('type', array(ShopAttribute::TYPE_DROPDOWN, ShopAttribute::TYPE_RADIO_LIST));
        $type = ShopProductType::model()->with(array('shopAttributes'))->findByPk($_GET['type_id'], $cr);

        $data = array();
        foreach ($type->shopAttributes as $attr) {
            $data[] = array(
                'id' => $attr->id,
                'title' => $attr->title,
            );
        }

        echo json_encode($data);
    }

    /**
     * @param $id ShopProductImage id
     */
    public function actionDeleteImage($id) {
        if (Yii::app()->request->getIsPostRequest()) {
            $model = ShopProductImage::model()->findByPk($id);
            if ($model)
                $model->delete();
        }
    }

    /**
     * Mass product update switch
     */
    public function actionUpdateIsActive() {
        $ids = Yii::app()->request->getPost('ids');
        $switch = (int) Yii::app()->request->getPost('switch');
        $models = ShopProduct::model()->findAllByPk($ids);
        foreach ($models as $product) {
            if (in_array($switch, array(0, 1))) {
                $product->switch = $switch;
                $product->save(false, false);
            }
        }
        echo Yii::t('core', 'SUCCESS_UPDATE');
    }

    /**
     * Delete products
     * @param array $id
     */
    public function actionDelete($id = array()) {
        if (Yii::app()->request->isPostRequest) {
            $model = ShopProduct::model()->findAllByPk($_REQUEST['id']);

            if (!empty($model)) {
                foreach ($model as $page)
                    $page->delete();
            }

            if (!Yii::app()->request->isAjaxRequest)
                $this->redirect('index');
        }
    }

    /**
     * Validate required shop attributes
     * @param ShopProduct $model
     * @return bool
     */
    public function validateAttributes(ShopProduct $model) {
        $attributes = $model->type->shopAttributes;

        if (empty($attributes) || $model->use_configurations)
            return true;

        $errors = false;
        foreach ($attributes as $attr) {
            if ($attr->required && !isset($_POST['ShopAttribute'][$attr->name])) {
                $errors = true;
                $model->addError($attr->name, Yii::t('ShopModule.admin', 'FIEND_REQUIRED', array('{FIELD}' => $attr->title)));
            }
        }

        return !$errors;
    }

    /**
     * Add option to shop attribute
     *
     * @throws CHttpException
     */
    public function actionAddOptionToAttribute() {
        $attribute = ShopAttribute::model()
                ->findByPk($_GET['attr_id']);

        if (!$attribute)
            throw new CHttpException(404, Yii::t('ShopModule.admin', 'ERR_LOAD_ATTR'));

        $attributeOption = new ShopAttributeOption;
        $attributeOption->attribute_id = $attribute->id;
        $attributeOption->value = $_GET['value'];
        $attributeOption->save(false, false, false);

        echo $attributeOption->id;
    }

    /**
     * Updates image titles
     */
    public function updateImageTitles() {
        if (sizeof(Yii::app()->request->getPost('image_titles', array()))) {
            foreach (Yii::app()->request->getPost('image_titles', array()) as $id => $title) {
                ShopProductImage::model()->updateByPk($id, array(
                    'title' => $title
                ));
            }
        }
    }

    /**
     * Render popup window
     */
    public function actionRenderCategoryAssignWindow() {

        $this->renderPartial('category_assign_window', array(), false, true);
    }

    /**
     * Render popup windows
     */
    public function actionRenderDuplicateProductsWindow() {
        $this->renderPartial('duplicate_products_window');
    }

    /**
     * Render popup windows
     */
    public function actionRenderProductsPriceWindow() {
        if (Yii::app()->request->isAjaxRequest) {
            $model = new ShopProduct();
            $this->renderPartial('products_price_window', array('model' => $model));
        } else {
            throw new CException(Yii::t('http_error', '403'), 403);
        }
    }

    /**
     * Set price products
     */
    public function actionSetProducts() {
        $request = Yii::app()->request;
        if ($request->isAjaxRequest) {
            $product_ids = $request->getPost('products', array());
            parse_str($request->getPost('data'), $price);
            $products = ShopProduct::model()->findAllByPk($product_ids);
            foreach ($products as $p) {
                if (isset($p)) {
                    if (!$p->currency_id || !$p->use_configurations) { //запрещаем редактирование товаров с привязанной ценой и/или концигурациями
                        $p->price = $price['ShopProduct']['price'];
                        if ($p->validate()) {
                            $p->save(false, false);
                        }
                    }
                }
            }
        } else {
            throw new CException(Yii::t('http_error', '403'), 403);
        }
    }

    /**
     * Duplicate products
     */
    public function actionDuplicateProducts() {
        //TODO: return ids to find products
        $product_ids = Yii::app()->request->getPost('products', array());
        parse_str(Yii::app()->request->getPost('duplicate'), $duplicates);

        if (!isset($duplicates['copy']))
            $duplicates['copy'] = array();

        $duplicator = new SProductsDuplicator;
        $ids = $duplicator->createCopy($product_ids, $duplicates['copy']);
        echo '/admin/shop/products/?ShopProduct[id]=' . implode(',', $ids);
    }

    /**
     * Assign categories to products
     */
    public function actionAssignCategories() {
        $categories = Yii::app()->request->getPost('category_ids');
        $products = Yii::app()->request->getPost('product_ids');

        if (empty($categories) || empty($products))
            return;

        $products = ShopProduct::model()->findAllByPk($products);

        foreach ($products as $p)
            $p->setCategories($categories, Yii::app()->request->getPost('main_category'));
    }

    /**
     * @param ShopProduct $model
     */
    public function updateMainImage(ShopProduct $model) {
        if (Yii::app()->request->getPost('mainImageId')) {
            // Clear current main image
            ShopProductImage::model()->updateAll(array('is_main' => 0), 'product_id=:pid', array(':pid' => $model->id));
            // Set new main image
            ShopProductImage::model()->updateByPk(Yii::app()->request->getPost('mainImageId'), array('is_main' => 1));
        }
    }

    /**
     * @param ShopProduct $model
     */
    public function handleUploadedImages(ShopProduct $model) {
        $images = CUploadedFile::getInstancesByName('ShopProductImages');
        if ($images && sizeof($images) > 0) {
            /** var $image CUploadedFile */
            foreach ($images as $image) {
                if (!ShopUploadedImage::hasErrors($image)) {
                    $model->addImage($image);
                } else {
                    $this->setFlashMessage(Yii::t('ShopModule.admin', 'ERR_LOAD_IMAGE', array('{NAME}' => $image->getName())));
                }
            }
        }
    }

}
