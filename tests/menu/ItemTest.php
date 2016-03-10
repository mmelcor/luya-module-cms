<?php

namespace tests\web\cms\menu;

use Yii;
use admin\models\User;
use cms\menu\Query;
use luyatest\LuyaWebTestCase;

class ItemTest extends LuyaWebTestCase
{
    public function testItemFunctions()
    {
        $obj = Yii::$app->menu->findOne(['id' => 1]);
        
        $this->assertEquals(1, $obj->id);
        $this->assertEquals("default", $obj->container);
        $this->assertEquals(1, $obj->navId);
        $this->assertEquals(0, $obj->parentNavId);
        $this->assertEquals("Homepage", $obj->title);
        $this->assertEquals(1, $obj->type);
        $this->assertEquals("", $obj->description);
        $this->assertEquals("homepage", $obj->alias);
        $this->assertEquals(1452169829, $obj->dateCreated);
        $this->assertEquals(0, $obj->dateUpdated);
        //$user->assertTrue($obj->userCreated instanceof User);
        //$user->assertTrue($obj->userUpdated instanceof User);
        $this->assertEquals("/luya/envs/dev/public_html/", $obj->link);
        $this->assertEquals(true, $obj->isActive);
        $this->assertEquals(1, $obj->depth);
        $this->assertEquals(false, $obj->parent);
        $this->assertEquals(0, count($obj->parents));
        $this->assertEquals(1, count($obj->teardown));
        $this->assertEquals(0, count($obj->children));
        $this->assertEquals(7, count($obj->siblings));
        $this->assertFalse($obj->hasChildren());
    }
    
    public function testChildItem()
    {
        $obj = Yii::$app->menu->findOne(['id' => 10]);
        $this->assertEquals("/luya/envs/dev/public_html/en/page-2/sub-page-2-of-2", $obj->link);
        $this->assertEquals(2, $obj->depth);
        $this->assertEquals(2, count($obj->siblings));
    }
    
    public function testFindHidden()
    {
        $this->assertFalse(Yii::$app->menu->findOne(['id' => 8]));
    }
    
    public function testFindOffline()
    {
        $this->assertFalse(Yii::$app->menu->findOne(['id' => 7]));
    }
    
    public function testFindHiddenAndOffline()
    {
        $this->assertFalse(Yii::$app->menu->findOne(['id' => 9]));
    }
    
    public function testFindHiddenWithHidden()
    {
        $this->assertInstanceOf("cms\menu\Item", Yii::$app->menu->find()->where(['id' => 8])->with('hidden')->one());
    }
    
    public function testFindOfflineWithHidden()
    {
        $this->assertFalse(Yii::$app->menu->find()->where(['id' => 7])->with('hidden')->one());
    }
    
    public function testFindHiddenAndOfflineWithHidden()
    {
        $this->assertFalse(Yii::$app->menu->find()->where(['id' => 9])->with('hidden')->one());
    }
    
    public function testRedirectInternal()
    {
        $obj = Yii::$app->menu->findOne(['id' => 5]);
        
        $this->assertEquals('/luya/envs/dev/public_html/en/page-1', $obj->link);
    }
    
    public function testRedirectExternal()
    {
        $obj = Yii::$app->menu->findOne(['id' => 6]);
    
        $this->assertEquals('https://luya.io', $obj->link);
    }
    
    // translation tests
    
    public function testHomeLanguageCompare()
    {
        $this->assertEquals("/luya/envs/dev/public_html/", (new Query())->where(['nav_id' => 1])->lang('en')->one()->link);
        $this->assertEquals("/luya/envs/dev/public_html/de/startseite", (new Query())->where(['nav_id' => 1])->lang('de')->one()->link);
    }
    
    public function testPage1LanguageCompare()
    {
        $this->assertEquals("/luya/envs/dev/public_html/en/page-1", (new Query())->where(['nav_id' => 2])->lang('en')->one()->link);
        $this->assertEquals("/luya/envs/dev/public_html/de/seite-1", (new Query())->where(['nav_id' => 2])->lang('de')->one()->link);
    }
    
    public function testInternalRedirectLanguageCompare()
    {
        $this->assertEquals("/luya/envs/dev/public_html/en/page-1", (new Query())->where(['nav_id' => 5])->lang('en')->one()->link);
        $this->assertEquals("/luya/envs/dev/public_html/de/seite-1", (new Query())->where(['nav_id' => 5])->lang('de')->one()->link);
    }
    
    public function testExternalRedirectLanguageCompare()
    {
        $this->assertEquals("https://luya.io", (new Query())->where(['nav_id' => 6])->lang('en')->one()->link);
        $this->assertEquals("https://luya.io", (new Query())->where(['nav_id' => 6])->lang('de')->one()->link);
    }
    
    public function testSubLanguageCompare()
    {
        $this->assertEquals("/luya/envs/dev/public_html/en/page-2/sub-page-2-of-2", (new Query())->where(['nav_id' => 10])->lang('en')->one()->link);
        $this->assertEquals("/luya/envs/dev/public_html/de/seite-2/unterseite-2-von-seite-2", (new Query())->where(['nav_id' => 10])->lang('de')->one()->link);
    }
    
    public function testCountLangaugeCompare()
    {
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 1])->lang('en')->one()->parents));
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 1])->lang('de')->one()->parents));
        
        $this->assertEquals(7, count((new Query())->where(['nav_id' => 1])->lang('en')->one()->siblings));
        $this->assertEquals(7, count((new Query())->where(['nav_id' => 1])->lang('de')->one()->siblings));
        
        $this->assertEquals(1, count((new Query())->where(['nav_id' => 1])->lang('en')->one()->teardown));
        $this->assertEquals(1, count((new Query())->where(['nav_id' => 1])->lang('de')->one()->teardown));
        
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 1])->lang('en')->one()->children));
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 1])->lang('de')->one()->children));
    }
    
    public function testCountLangaugeAsSubCompare()
    {
        $this->assertEquals(1, count((new Query())->where(['nav_id' => 10])->lang('en')->one()->parents));
        $this->assertEquals(1, count((new Query())->where(['nav_id' => 10])->lang('de')->one()->parents));
    
        $this->assertEquals(2, count((new Query())->where(['nav_id' => 10])->lang('en')->one()->siblings));
        $this->assertEquals(2, count((new Query())->where(['nav_id' => 10])->lang('de')->one()->siblings));
    
        $this->assertEquals(2, count((new Query())->where(['nav_id' => 10])->lang('en')->one()->teardown));
        $this->assertEquals(2, count((new Query())->where(['nav_id' => 10])->lang('de')->one()->teardown));
    
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 10])->lang('en')->one()->children));
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 10])->lang('de')->one()->children));
    }
    
    public function testCountLangaugeAsPageWithChildrenCompare()
    {
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 3])->lang('en')->one()->parents));
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 3])->lang('de')->one()->parents));
    
        $this->assertEquals(7, count((new Query())->where(['nav_id' => 3])->lang('en')->one()->siblings));
        $this->assertEquals(7, count((new Query())->where(['nav_id' => 3])->lang('de')->one()->siblings));
    
        $this->assertEquals(1, count((new Query())->where(['nav_id' => 3])->lang('en')->one()->teardown));
        $this->assertEquals(1, count((new Query())->where(['nav_id' => 3])->lang('de')->one()->teardown));
    
        $this->assertEquals(2, count((new Query())->where(['nav_id' => 3])->lang('en')->one()->children));
        $this->assertEquals(2, count((new Query())->where(['nav_id' => 3])->lang('de')->one()->children));
    }
    
    public function testCountLangaugeCompareAsMenuFindOne()
    {
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 1])->one()->parents));
        $this->assertEquals(0, count(Yii::$app->menu->findOne(['nav_id' => 1])->parents));
    
        $this->assertEquals(7, count((new Query())->where(['nav_id' => 1])->lang('en')->one()->siblings));
        $this->assertEquals(7, count((new Query())->where(['nav_id' => 1])->lang('de')->one()->siblings));
    
        $this->assertEquals(1, count((new Query())->where(['nav_id' => 1])->lang('en')->one()->teardown));
        $this->assertEquals(1, count((new Query())->where(['nav_id' => 1])->lang('de')->one()->teardown));
    
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 1])->lang('en')->one()->children));
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 1])->lang('de')->one()->children));
    }
    
    public function testCountLangaugeAsSubCompareAsMenuFindOne()
    {
        $this->assertEquals(1, count((new Query())->where(['nav_id' => 10])->lang('en')->one()->parents));
        $this->assertEquals(1, count((new Query())->where(['nav_id' => 10])->lang('de')->one()->parents));
    
        $this->assertEquals(2, count((new Query())->where(['nav_id' => 10])->lang('en')->one()->siblings));
        $this->assertEquals(2, count((new Query())->where(['nav_id' => 10])->lang('de')->one()->siblings));
    
        $this->assertEquals(2, count((new Query())->where(['nav_id' => 10])->lang('en')->one()->teardown));
        $this->assertEquals(2, count((new Query())->where(['nav_id' => 10])->lang('de')->one()->teardown));
    
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 10])->lang('en')->one()->children));
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 10])->lang('de')->one()->children));
    }
    
    public function testCountLangaugeAsPageWithChildrenCompareAsMenuFindOne()
    {
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 3])->lang('en')->one()->parents));
        $this->assertEquals(0, count((new Query())->where(['nav_id' => 3])->lang('de')->one()->parents));
    
        $this->assertEquals(7, count((new Query())->where(['nav_id' => 3])->lang('en')->one()->siblings));
        $this->assertEquals(7, count((new Query())->where(['nav_id' => 3])->lang('de')->one()->siblings));
    
        $this->assertEquals(1, count((new Query())->where(['nav_id' => 3])->lang('en')->one()->teardown));
        $this->assertEquals(1, count((new Query())->where(['nav_id' => 3])->lang('de')->one()->teardown));
    
        $this->assertEquals(2, count((new Query())->where(['nav_id' => 3])->lang('en')->one()->children));
        $this->assertEquals(2, count((new Query())->where(['nav_id' => 3])->lang('de')->one()->children));
    }
    
}