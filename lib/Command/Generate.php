<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Command;

use OCA\TwoFactorAdmin\Service\CodeStorage;
use OCP\IUserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Generate extends Command {

	/** @var CodeStorage */
	private $codeStorage;

	/** @var IUserManager */
	private $userManager;

	public function __construct(CodeStorage $codeStorage, IUserManager $userManager) {
		parent::__construct();
		$this->codeStorage = $codeStorage;
		$this->userManager = $userManager;
	}

	protected function configure() {
		$this->setName('twofactorauth:admin:generate-code');
		$this->setDescription('Generate a one-time 2FA code for users to log into their account');
		$this->addArgument('uid', InputArgument::REQUIRED);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$userId = $input->getArgument('uid');

		$user = $this->userManager->get($userId);

		if (is_null($user)) {
			$output->writeln("<error>Invalid UID</error>");
			return 1;
		}

		if ($this->codeStorage->hasCode($user)) {
			// TODO: abort? Ask for confirmattion? Add `-f|--force` flag?
			$output->writeln("<info>There is an existing code that will be overwritten.</info>");
			$output->writeln("");
		}

		$code = $this->codeStorage->generateCode($user);
		$output->writeln("Generated new one-time code for <options=bold>$userId</>: <info>$code</info>");
		$ttlInHours = CodeStorage::CODE_TTL / 3600;
		$output->writeln("This code is valid for $ttlInHours hours.");

		return 0;
	}

}
