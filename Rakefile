basedir = "."
source  = "#{basedir}/library/ImboClientCli"
build   = "#{basedir}/build"
logs    = "#{build}/logs"

desc "Clean build dirs"
task :clean do
    system "rm -rf #{build}"

    system "mkdir #{build}"
    system "mkdir #{build}/coverage"
    system "mkdir #{build}/docs"
    system "mkdir #{build}/pdepend"
    system "mkdir #{logs}"
end

desc "Check syntax on all php files in the project"
task :lint do
    `git ls-files "*.php"`.split("\n").each do |f|
        begin
            sh %{php -l #{f}}
        rescue Exception
            exit 1
        end
    end
end

desc "Run unit tests using PHPUnit (configuration in phpunit.xml)"
task :test do
    system "phpunit"
end

desc "Generate API documentation using DocBlox (configuration in docblox.xml)"
task :docs do
    system "docblox"
end

desc "Generate phploc data"
task :"phploc-ci" do
    system "phploc --log-csv #{logs}/phploc.csv --log-xml #{logs}/phploc.xml #{source}"
end

desc "Run phploc"
task :phploc do
    system "phploc #{source}"
end

desc "Generate checkstyle.xml using PHP_CodeSniffer"
task :"codesniffer-ci" do
    system "phpcs --report=checkstyle --report-file=#{logs}/checkstyle.xml --standard=Imbo #{source}"
end

desc "Check syntax with PHP_CodeSniffer"
task :codesniffer do
    system "phpcs --standard=Imbo #{source}"
end

desc "Generate pmd-cpd.xml using PHPCPD"
task :"cpd-ci" do
    system "phpcpd --log-pmd #{logs}/pmd-cpd.xml #{source}"
end

desc "Run PHPCPD"
task :"cpd" do
    system "phpcpd --log-pmd #{logs}/pmd-cpd.xml #{source}"
end

desc "Generate jdepend.xml and software metrics charts using PHP_Depend"
task :"pdepend-ci" do
    system "pdepend --jdepend-xml=#{logs}/jdepend.xml --jdepend-chart=#{build}/pdepend/dependencies.svg --overview-pyramid=#{build}/pdepend/overview-pyramid.svg #{source}"
end

desc "Generate pmd.xml and pmd.html using PHPMD (configuration in phpmd.xml)"
task :"pmd-ci" do
    system "phpmd #{source} xml #{basedir}/phpmd.xml --reportfile #{logs}/pmd.xml"
    system "phpmd #{source} html #{basedir}/phpmd.xml --reportfile #{logs}/pmd.html"
end

desc "Continuous integration task"
task :ci => [:clean, :test, :docs, :"phploc-ci", :"codesniffer-ci", :"cpd-ci", :"pdepend-ci", :"pmd-ci"]

desc "Default task"
task :default => [:clean, :lint, :test, :docs, :phploc, :codesniffer, :cpd]
