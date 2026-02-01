<?php

class Blade {
    protected $viewPath;
    protected $cachePath;
    
    public function __construct($viewPath, $cachePath = null) {
        $this->viewPath = $viewPath;
        $this->cachePath = $cachePath ?: __DIR__ . '/../../storage/views';
        
        // Create cache directory
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }
    
    public function render($view, $data = []) {
        $viewFile = $this->viewPath . '/' . str_replace('.', '/', $view) . '.blade.php';
        
        if (!file_exists($viewFile)) {
            die("View not found: {$view} at {$viewFile}");
        }
        
        // Compile Blade to PHP
        $phpContent = $this->compileFile($viewFile);
        
        // Create cache file
        $hash = md5($viewFile . filemtime($viewFile));
        $cacheFile = $this->cachePath . '/' . $hash . '.php';
        file_put_contents($cacheFile, $phpContent);
        
        // Render with data
        extract($data);
        ob_start();
        include $cacheFile;
        return ob_get_clean();
    }
    
    protected function compileFile($file) {
        $content = file_get_contents($file);
        return $this->compile($content, $file);
    }
    
    protected function compile($content, $file = null) {
        // Handle template inheritance first
        if (strpos($content, '@extends') !== false) {
            $content = $this->compileInheritance($content);
        }
        
        // Convert Blade directives
        $content = $this->compileDirectives($content);
        
        // Convert Blade echoes
        $content = $this->compileEchos($content);
        
        // Clean up
        $content = trim($content);
        
        // Add PHP opening tag if not present
        if (strpos($content, '<?php') === false) {
            $content = '<?php' . "\n?>" . $content;
        }
        
        return $content;
    }
    
    protected function compileInheritance($content) {
        // Extract @extends
        preg_match('/@extends\(\s*[\'"](.+?)[\'"]\s*\)/', $content, $extendsMatch);
        if (!$extendsMatch) {
            return $content;
        }
        
        $layout = $extendsMatch[1];
        $content = str_replace($extendsMatch[0], '', $content);
        
        // Extract @section
        preg_match('/@section\(\s*[\'"](.+?)[\'"]\s*\)(.*?)@endsection/s', $content, $sectionMatch);
        if (!$sectionMatch) {
            return $content;
        }
        
        $sectionName = trim($sectionMatch[1]);
        $sectionContent = trim($sectionMatch[2]);
        
        // Load layout
        $layoutFile = $this->viewPath . '/' . str_replace('.', '/', $layout) . '.blade.php';
        if (!file_exists($layoutFile)) {
            return $content;
        }
        
        $layoutContent = file_get_contents($layoutFile);
        
        // Replace @yield in layout
        $layoutContent = str_replace("@yield('{$sectionName}')", $sectionContent, $layoutContent);
        $layoutContent = str_replace('@yield("' . $sectionName . '")', $sectionContent, $layoutContent);
        
        return $layoutContent;
    }
    
    protected function compileDirectives($content) {
        $directives = [
            // @auth directive
            '/@auth(\s*)/' => '<?php if(isset($_SESSION[\'user_id\'])): ?>$1',
            '/@endauth/' => '<?php endif; ?>',
            
            // @if directive
            '/@if\s*\((.*)\)/' => '<?php if($1): ?>',
            '/@endif/' => '<?php endif; ?>',
            
            // @foreach directive
            '/@foreach\s*\((.*)\)/' => '<?php foreach($1): ?>',
            '/@endforeach/' => '<?php endforeach; ?>',
            
            // @csrf directive
            '/@csrf/' => '<?php echo \'<input type="hidden" name="_token" value="\' . ($_SESSION[\'csrf_token\'] ?? \'\') . \'">\'; ?>',
        ];
        
        foreach ($directives as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }
        
        return $content;
    }
    
    protected function compileEchos($content) {
        // {{ $var }} - escaped output
        $content = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?php echo htmlspecialchars($1 ?? \'\'); ?>', $content);
        
        // {!! $var !!} - raw output
        $content = preg_replace('/\{!!\s*(.+?)\s*!!\}/', '<?php echo $1; ?>', $content);
        
        return $content;
    }
}