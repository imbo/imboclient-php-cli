require 'date'
require 'digest/md5'
require 'fileutils'
require 'nokogiri'
require 'json'

basedir = "."
build   = "#{basedir}/build"
source  = "#{basedir}/src/ImboClientCli"
logs    = "#{build}/logs"

desc "Task used by Jenkins-CI"
task :jenkins => [:prepare, :lint, :installdep, :test, :apidocs, :cs_ci]

desc "Task used by Travis-CI"
task :travis => [:installdep, :test]

desc "Default task"
task :default => [:prepare, :lint, :installdep, :test, :apidocs, :cs]

desc "Clean up and create artifact directories"
task :prepare do
  FileUtils.rm_rf build
  FileUtils.mkdir build

  ["coverage", "logs", "docs"].each do |d|
    FileUtils.mkdir "#{build}/#{d}"
  end
end

desc "Check syntax on all php files in the project"
task :lint do
  lintCache = "#{basedir}/.lintcache"

  begin
    sums = JSON.parse(IO.read(lintCache))
  rescue Exception => foo
    sums = {}
  end

  `git ls-files "*.php"`.split("\n").each do |f|
    f = File.absolute_path(f)
    md5 = Digest::MD5.hexdigest(File.read(f))

    next if sums[f] == md5

    sums[f] = md5

    begin
      sh %{php -l #{f}}
    rescue Exception
      exit 1
    end
  end

  IO.write(lintCache, JSON.dump(sums))
end

desc "Install dependencies"
task :installdep do
  if ENV["TRAVIS"] == "true"
    system "composer self-update"
    system "composer --no-ansi install --dev"
  else
    Rake::Task["install_composer"].invoke
    system "php -d \"apc.enable_cli=0\" composer.phar install --dev"
  end
end

desc "Update dependencies"
task :updatedep do
  Rake::Task["install_composer"].invoke
  system "php -d \"apc.enable_cli=0\" composer.phar update --dev"
end

desc "Install/update composer itself"
task :install_composer do
  if File.exists?("composer.phar")
    system "php -d \"apc.enable_cli=0\" composer.phar self-update"
  else
    system "curl -s http://getcomposer.org/installer | php -d \"apc.enable_cli=0\""
  end
end

desc "Run tests"
task :test do
  if ENV["TRAVIS"] == "true"
    begin
      sh %{vendor/bin/phpunit --verbose -c tests/phpunit.xml.travis}
    rescue Exception
      exit 1
    end
  else
    begin
      sh %{vendor/bin/phpunit --verbose -c tests --coverage-html build/coverage --coverage-clover build/logs/clover.xml --log-junit build/logs/junit.xml}
    rescue Exception
      exit 1
    end
  end
end

desc "Generate API documentation"
task :apidocs do
  system "phpdoc -d #{source} -t #{build}/docs --title 'API documentation for ImboClientCli'"
end

desc "Check coding standard"
task :cs do
  system "phpcs --standard=Imbo #{source}"
end

desc "Check coding standard and generate checkstyle logs"
task :cs_ci do
  system "phpcs --report=checkstyle --report-file=#{logs}/checkstyle.xml --standard=Imbo #{source}"
end
