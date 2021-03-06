<?php

namespace {

    use SilverStripe\AssetAdmin\Forms\UploadField;
    use SilverStripe\Assets\Image;
    use SilverStripe\CMS\Model\SiteTree;
    use SilverStripe\Forms\CheckboxField;
    use SilverStripe\Forms\DropdownField;
    use SilverStripe\Forms\HiddenField;
    use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
    use SilverStripe\Forms\ReadonlyField;
    use SilverStripe\Forms\TextField;
    use SilverStripe\ORM\DataObject;

    class CardItem extends DataObject
    {
        private static $default_sort  = 'Sort';

        private static $db = [
            'Name'        => 'Text',
            'Width'       => 'Text',
            'Content'     => 'HTMLText',
            'AnimationType' => 'Text',
            'ExternalLink'  => 'Text',
            'Archived'   => 'Boolean',
            'Sort'       => 'Int'
        ];

        private static $has_one = [
            'Parent'  => Card::class,
            'Page'    => SiteTree::class,
            'BgImage' => Image::class
        ];

        private static $summary_fields = [
            'Name',
            'BgImage.CMSThumbnail' => 'Image',
            'Width',
            'Status'
        ];

        public function getCMSFields()
        {
            $fields = parent::getCMSFields(); // TODO: Change the autogenerated stub
            $fields->removeByName('ParentID');
            $fields->addFieldToTab('Root.Main', ReadonlyField::create('ParentRO', 'Parent', $this->Parent()->Name));

            $fields->addFieldToTab('Root.Main', TextField::create('Name'));
            $fields->addFieldToTab('Root.Main', UploadField::create('BgImage', 'Card image')->setFolderName('Card_Images'));
            $fields->addFieldToTab('Root.Main', DropdownField::create('PageID', 'Select page to link', SiteTree::get()->map('ID','Title'))
                ->setEmptyString('(Select one)')
                ->setDescription('This will ignore if you have placed an External link.'));
            $fields->addFieldToTab('Root.Main', TextField::create('ExternalLink'));
            $fields->addFieldToTab('Root.Main', DropdownField::create('Width', 'Select width size',
                Width::get()->map('Name','Name'))->setRightTitle('Tip: col-lg-6 is equivalent to a 50% width size'));
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content'));
            $fields->addFieldToTab('Root.Animation', DropdownField::create('AnimationType','Select animation', Animations::get()->filter('Archived', false)->map('Name', 'Name')));
            $fields->addFieldToTab('Root.Main', CheckboxField::create('Archived'));
            $fields->addFieldToTab('Root.Main', HiddenField::create('Sort'));

            return $fields;
        }

        public function getStatus()
        {
            if($this->Archived == 1) return _t('GridField.Archived', 'Archived');
            return _t('GridField.Live', 'Live');
        }
    }
}
