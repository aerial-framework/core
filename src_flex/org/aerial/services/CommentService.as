package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.CommentVO;
	import org.aerial.config.Config;

	public class CommentService extends AbstractService
	{
		public function CommentService()
		{
			super("CommentService", Config.SERVER_URL, CommentVO);
		}
	}
}