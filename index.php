<?php
require_once('/PATH/TO/NOLOH');

class ImageViewer extends WebPage
{
	const Folder = 'Images';
	private $FileList;
	private $ItemViewer;
	
	function ImageViewer()
	{
		parent::WebPage('Image Viewer');
		$this->Init();
	}
	function Init()
	{
		$this->FileList = new TreeList(0, 0, 200, '100%');
		$src = implode('/', URL::$TokenChain->Elements);
		$this->ItemViewer = new IFrame($src, $this->FileList->Right, 0, $this->Width - $this->FileList->Width, '100%');
		$this->Controls->AddRange($this->FileList, $this->ItemViewer);
		$this->CreateDirectoryTree(self::Folder, $this->FileList);
	}
	function CreateDirectoryTree($path, $list)
	{
		$dirHandle = @opendir($path) or System::Log("Unable to open directory $path");
	    while($file = readdir($dirHandle))
	    	if($file != '.' && $file != '..')
		    {
		        $subPath = $path . '/'. $file;
		        if(is_dir($subPath))
		        {
		            $list->TreeNodes->Add($folderNode = &new TreeNode($file));
		            $this->CreateDirectoryTree($subPath, $folderNode);
		        }
		        else
		        {
		        	$list->TreeNodes->Add($fileNode = &new TreeNode($file));
		        	$fileNode->Click = new ClientEvent('_NSetURL', '#/'.$subPath);
		         	$fileNode->Click[] = new ServerEvent($this->ItemViewer, 'SetSrc', $subPath);
		         	if($this->ItemViewer->Src == $subPath)
		         		$fileNode->Selected = true;
		        }
		    }
	    closedir($dirHandle);
	}
}
?>
